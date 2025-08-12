<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use App\Models\FacebookBusinessAccount;
use App\Models\FacebookPage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class FacebookSocialAuthController extends Controller
{
    /**
     * Redirect to Facebook OAuth
     */
    public function redirectToFacebook()
    {
        try {
            // Check if Facebook is configured
            if (empty(config('services.facebook.client_id')) || 
                config('services.facebook.client_id') === 'your_facebook_app_id') {
                return redirect()->route('team.dashboard')
                    ->with('error', 'Facebook App is not configured. Please update your .env file with valid Facebook credentials.');
            }

            return Socialite::driver('facebook')
                ->scopes([
                    'leads_retrieval',
                    'pages_show_list',
                    'pages_manage_ads',
                    'pages_read_engagement',
                    'pages_manage_metadata',
                    'email',
                    'business_management',
                    'ads_management',
                    // 'pages_read_user_content'
                ])
                ->redirectUrl(route('facebook.auth.callback'))
                ->redirect();

        } catch (Exception $e) {
            Log::error('Facebook OAuth redirect error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('team.dashboard')
                ->with('error', 'Failed to initiate Facebook connection: ' . $e->getMessage());
        }
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $token = $facebookUser->token;
            $currentUser = Auth::user();
            
            if (!$currentUser) {
                return redirect()->route('team.login')
                    ->with('error', 'You must be logged in to connect Facebook account.');
            }

            // Get Facebook Business Accounts
            $businessAccountsResponse = Http::get("https://graph.facebook.com/v18.0/me/businesses", [
                'access_token' => $token,
                'fields' => 'id,name,verification_status,primary_page'
            ]);

            if ($businessAccountsResponse->failed()) {
                throw new Exception('Failed to fetch business accounts from Facebook');
            }

            $businessAccounts = $businessAccountsResponse->json()['data'] ?? [];
            
            // Get Facebook Pages
            $pagesResponse = Http::get("https://graph.facebook.com/v18.0/me/accounts", [
                'access_token' => $token,
                'fields' => 'id,name,access_token,category,category_list,verification_status'
            ]);

            if ($pagesResponse->failed()) {
                throw new Exception('Failed to fetch pages from Facebook');
            }

            $pages = $pagesResponse->json()['data'] ?? [];

            // Create or update Facebook Business Account
            $businessAccount = null;
            if (!empty($businessAccounts)) {
                $firstBusiness = $businessAccounts[0];
                
                $businessAccount = FacebookBusinessAccount::updateOrCreate([
                    'facebook_business_id' => $firstBusiness['id'],
                    'branch_id' => $currentUser->branch_id
                ], [
                    'business_name' => $firstBusiness['name'] ?? 'Facebook Business',
                    'access_token' => $token,
                    'app_id' => config('services.facebook.client_id'),
                    'app_secret' => config('services.facebook.client_secret'),
                    'status' => 'connected',
                    'token_expires_at' => now()->addDays(60), // Facebook tokens typically last 60 days
                ]);
            } else {
                // If no business account, create one with user info
                $businessAccount = FacebookBusinessAccount::updateOrCreate([
                    'facebook_business_id' => $facebookUser->getId(),
                    'branch_id' => $currentUser->branch_id
                ], [
                    'business_name' => $facebookUser->getName() . "'s Business",
                    'access_token' => $token,
                    'app_id' => config('services.facebook.client_id'),
                    'app_secret' => config('services.facebook.client_secret'),
                    'status' => 'connected',
                    'token_expires_at' => now()->addDays(60)
                ]);
            }

            // Save Facebook Pages
            foreach ($pages as $page) {
                FacebookPage::updateOrCreate([
                    'facebook_page_id' => $page['id'],
                    'facebook_business_account_id' => $businessAccount->id
                ], [
                    'page_name' => $page['name'],
                    'page_access_token' => $page['access_token'],
                    'is_active' => true
                ]);
            }

            // Log successful connection
            Log::info('Facebook account connected successfully', [
                'user_id' => $currentUser->id,
                'business_account_id' => $businessAccount->id,
                'pages_count' => count($pages)
            ]);

            return redirect()->route('facebook.dashboard')
                ->with('success', 'Facebook account connected successfully! Found ' . count($pages) . ' page(s).');

        } catch (Exception $e) {
            Log::error('Facebook OAuth callback error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('team.dashboard')
                ->with('error', 'Failed to connect Facebook account: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Facebook account
     */
    public function disconnect()
    {
        try {
            $user = Auth::user();
            $businessAccount = FacebookBusinessAccount::where('branch_id', $user->branch_id)->first();

            if ($businessAccount) {
                // Update status instead of deleting to preserve data
                $businessAccount->update([
                    'status' => 'disconnected',
                    'access_token' => null
                ]);

                // Deactivate associated pages
                $businessAccount->facebookPages()->update(['status' => 'inactive']);

                Log::info('Facebook account disconnected', [
                    'user_id' => $user->id,
                    'business_account_id' => $businessAccount->id
                ]);

                return redirect()->route('team.dashboard')
                    ->with('success', 'Facebook account disconnected successfully.');
            }

            return redirect()->route('team.dashboard')
                ->with('info', 'No Facebook account was connected.');

        } catch (Exception $e) {
            Log::error('Facebook disconnect error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('team.dashboard')
                ->with('error', 'Failed to disconnect Facebook account: ' . $e->getMessage());
        }
    }

    /**
     * Refresh Facebook access token
     */
    public function refreshToken()
    {
        try {
            $user = Auth::user();
            $businessAccount = FacebookBusinessAccount::where('branch_id', $user->branch_id)->first();

            if (!$businessAccount || !$businessAccount->access_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Facebook account connected'
                ]);
            }

            // Try to refresh the token by making a test API call
            $response = Http::get("https://graph.facebook.com/v18.0/me", [
                'access_token' => $businessAccount->access_token,
                'fields' => 'id,name'
            ]);

            if ($response->successful()) {
                $businessAccount->update([
                    'last_sync_at' => now(),
                    'status' => 'active'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Token is still valid'
                ]);
            } else {
                // Token is invalid, mark as expired
                $businessAccount->update(['status' => 'token_expired']);

                return response()->json([
                    'success' => false,
                    'message' => 'Token has expired. Please reconnect your Facebook account.'
                ]);
            }

        } catch (Exception $e) {
            Log::error('Facebook token refresh error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token: ' . $e->getMessage()
            ]);
        }
    }
}
