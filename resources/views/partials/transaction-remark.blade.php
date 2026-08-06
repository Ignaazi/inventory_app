@php
    $transactionType = strtoupper((string) ($transactionType ?? 'TRANSACTION'));
    $fallbackRemark = 'AUTOMATED ' . $transactionType;
    $rawRemark = trim((string) ($remark ?? ''));
    $remarkText = $rawRemark !== '' ? strtoupper($rawRemark) : $fallbackRemark;

    if (preg_match('/^AUTOMATIC(?: STOCK)?\s+(IN|OUT|RETURN|DISPOSAL)\b/i', $rawRemark, $matches)) {
        $remarkText = 'AUTOMATED ' . strtoupper($matches[1]);
    } elseif (preg_match('/^AUTOMATED\s+STOCK\s+(IN|OUT|RETURN|DISPOSAL)\b/i', $rawRemark, $matches)) {
        $remarkText = 'AUTOMATED ' . strtoupper($matches[1]);
    } elseif (preg_match('/^AUTOMATED\s+(IN|OUT|RETURN|DISPOSAL)\s+VIA\b/i', $rawRemark, $matches)) {
        $remarkText = 'AUTOMATED ' . strtoupper($matches[1]);
    }
@endphp

<span class="remark-cell block truncate" title="{{ $rawRemark !== '' ? $rawRemark : $fallbackRemark }}">
    {{ $remarkText }}
</span>
