<x-email.layout :title="'Welcome to ' . config('app.name') . ' - Your Account Has Been Created'">
    <tr>
        <td class="content-block">
            Hello {{$content['name']}},
        </td>
    </tr>
    <tr>
        <td class="content-block">
            Welcome to {{config('app.name')}}! Your account has been successfully created and you can now access our system.
        </td>
    </tr>
    <tr>
        <td class="content-block">
            <strong>Your Login Credentials</strong>
            <br>
            Username: {{$content['username']}}
            <br>
            Password: {{$content['password']}}
            <br>
            <a href="{{ $content['app_url'] }}" target="_blank">Click here to login</a>
        </td>
    </tr>
    <tr>
        <td class="content-block">
            Thank you
        </td>
    </tr>
    <tr>
        <td class="content-block">
            {{ config('app.name') }}
        </td>
    </tr>
</x-email.layout>