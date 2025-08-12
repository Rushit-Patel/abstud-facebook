<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{{ $title ?? config('app.name') }}</title>
        <style>
            body{background-color:#f6f6f6;-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100%!important;height:100%;line-height:1.6}*{margin:0;padding:0;font-family:"Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;box-sizing:border-box;font-size:14px}body{display:block;margin:8px}*{margin:0;padding:0;font-family:"Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;box-sizing:border-box;font-size:14px}table{display:table;border-collapse:separate;border-spacing:2px;border-color:grey}.body-wrap{background-color:#f6f6f6;width:100%}tbody{display:table-row-group;vertical-align:middle;border-color:inherit}td,th{display:table-cell;vertical-align:inherit}.container{display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important}table td{vertical-align:top}.content{max-width:600px;margin:0 auto;display:block;padding:20px}.main{background:#fff;border:1px solid #e9e9e9;border-radius:3px}tr{display:table-row;vertical-align:inherit;border-color:inherit}strong,b{font-weight:700}.content-block{padding:0 0 20px}a{color:#1a98d6;text-decoration:underline}.content-wrap{padding:20px}
        </style>
    </head>
    <body>
        @php
            $settings = \App\Models\CompanySetting::getSettings();
            $companyLogo = null;
            $companyName = config('app.name');
            
            if ($settings) {
                $companyName = $settings->company_name ?? config('app.name');
                if ($settings->company_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings->company_logo)) {
                    $companyLogo = \Illuminate\Support\Facades\Storage::disk('public')->url($settings->company_logo);
                }
            }
        @endphp
        
        <table class="body-wrap">
            <tbody>
                <tr>
                    <td></td>
                    <td class="container" width="600">
                        <div class="content">
                            <table class="main" width="100%" cellpadding="0" cellspacing="0">
                                <tbody>
                                    @if($companyLogo)
                                        <tr>
                                            <td style="margin-top: 20px; ">
                                                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="max-width: 40%; height: auto; display: block; margin: 10px auto;">
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="content-wrap">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    {{ $slot }}
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
