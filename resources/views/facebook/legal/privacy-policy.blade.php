<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Facebook Integration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
                    <p class="text-gray-600">Facebook Integration Service</p>
                    <p class="text-sm text-gray-500 mt-2">Last updated: {{ date('F j, Y') }}</p>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Information We Collect</h2>
                    <p class="text-gray-700 mb-6">
                        When you use our Facebook Integration service, we collect and process the following types of information:
                    </p>
                    <ul class="list-disc pl-6 mb-6 text-gray-700">
                        <li><strong>Facebook Account Information:</strong> Basic profile information from your Facebook account when you connect it to our service</li>
                        <li><strong>Business Account Data:</strong> Information about your Facebook Business account, including business name and account ID</li>
                        <li><strong>Page Information:</strong> Details about Facebook pages you manage, including page names, IDs, and access tokens</li>
                        <li><strong>Lead Form Data:</strong> Information from Facebook Lead Ad forms, including form structure and field mappings</li>
                        <li><strong>Lead Information:</strong> Data submitted by users through your Facebook Lead Ad forms</li>
                        <li><strong>Usage Data:</strong> Information about how you use our service, including sync activities and integration settings</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. How We Use Your Information</h2>
                    <p class="text-gray-700 mb-6">We use the information we collect to:</p>
                    <ul class="list-disc pl-6 mb-6 text-gray-700">
                        <li>Facilitate the integration between your Facebook Business account and our platform</li>
                        <li>Sync and process leads from your Facebook Lead Ad campaigns</li>
                        <li>Map Facebook lead data to your internal system fields</li>
                        <li>Provide webhooks for real-time lead notifications</li>
                        <li>Monitor and maintain the quality of our service</li>
                        <li>Provide customer support and troubleshooting</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Information Sharing</h2>
                    <p class="text-gray-700 mb-6">
                        We do not sell, trade, or rent your personal information to third parties. We may share information in the following circumstances:
                    </p>
                    <ul class="list-disc pl-6 mb-6 text-gray-700">
                        <li><strong>With Your Consent:</strong> When you explicitly authorize us to share specific information</li>
                        <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our service</li>
                        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and the rights of others</li>
                        <li><strong>Business Transfers:</strong> In connection with any merger, sale of assets, or acquisition</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Data Security</h2>
                    <p class="text-gray-700 mb-6">
                        We implement appropriate technical and organizational measures to protect your information against unauthorized access, alteration, disclosure, or destruction. This includes:
                    </p>
                    <ul class="list-disc pl-6 mb-6 text-gray-700">
                        <li>Encryption of data in transit and at rest</li>
                        <li>Regular security assessments and updates</li>
                        <li>Access controls and authentication mechanisms</li>
                        <li>Secure handling of Facebook access tokens</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Data Retention</h2>
                    <p class="text-gray-700 mb-6">
                        We retain your information for as long as necessary to provide our services and fulfill the purposes outlined in this privacy policy. Lead data is retained according to your account settings and applicable legal requirements.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Your Rights</h2>
                    <p class="text-gray-700 mb-6">You have the right to:</p>
                    <ul class="list-disc pl-6 mb-6 text-gray-700">
                        <li>Access and review the information we have about you</li>
                        <li>Correct inaccurate or incomplete information</li>
                        <li>Request deletion of your information (subject to legal obligations)</li>
                        <li>Disconnect your Facebook account from our service at any time</li>
                        <li>Export your data in a portable format</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Facebook Platform Policy Compliance</h2>
                    <p class="text-gray-700 mb-6">
                        Our service complies with Facebook's Platform Policy and Terms of Service. We use Facebook's Graph API in accordance with their guidelines and maintain appropriate permissions for accessing your Facebook data.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Changes to This Policy</h2>
                    <p class="text-gray-700 mb-6">
                        We may update this privacy policy from time to time. We will notify you of any material changes by posting the new privacy policy on this page and updating the "Last updated" date.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Contact Information</h2>
                    <p class="text-gray-700 mb-6">
                        If you have any questions about this privacy policy or our data practices, please contact us at:
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <p class="text-gray-700"><strong>Email:</strong> projects@abstud.io</p>
                        <p class="text-gray-700"><strong>Address:</strong> 1201, The Capital 2, Science City Rd, Sola, Ahmedabad, Gujarat 380060</p>
                        <p class="text-gray-700"><strong>Phone:</strong> +91 90995 89276</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <a href="{{ route('facebook.terms-of-service') }}" class="text-blue-600 hover:text-blue-800 mr-4">Terms of Service</a>
                <a href="{{ route('facebook.dashboard') }}" class="text-gray-600 hover:text-gray-800">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
