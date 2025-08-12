<x-email.layout title="{{ $emailTemplate->subject }}">
    {!! $emailTemplate->html_template !!}
</x-email.layout>