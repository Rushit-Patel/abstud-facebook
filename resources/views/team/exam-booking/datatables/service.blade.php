<div class="grid grid-cols-1 items-center gap-2">
    <span class="font-medium">
        Service -
        <span class=" cursor-pointer hover:underline transition">{{ $service }}</span>
    </span>
    <span class="font-medium">
        Total Amount -
        <span class="copy-text cursor-pointer hover:underline transition">{{ $total_amount }}</span>
    </span>
    <span class="font-medium truncate">
        Discount -
        <span class="copy-text cursor-pointer hover:underline transition">{{ $discount }}</span>
    </span>
    <span class="font-medium truncate">
        Payable Amount -
        <span class="copy-text cursor-pointer hover:underline transition">{{ $payable_amount }}</span>
    </span>
</div>
