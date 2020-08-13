<?php

return [
    'alipay' => [
        'app_id'         => '2021000116688189',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0NyYWfiHq68EtnqJacyWX2S3tw/IytYznGXPVrO5i9rb/MZsPlaKmEX/ROTbeQfbssw1a2XoBmkbWVaCtUA+noU8hKTLJ2bABvZb3P8qOnMeq4c22CoFTeEBzPcT3hFryPjW0OdF8JQjRzsZ/xNWEHpAN9mvmCQiZlZC8M3kFL4HsyKsir2jnvH0UOJJGArbDbsVa3Cut3xk0OSwd0IDE8+nbCWKH9h4F39KiQiV+Usy49NvSG6PNn9YTcrlFSbfpamOFR/QUIRfCC5kHOL4vqIJFglyvx5BwUB3YTm4EBNuMns+piLq8bSLWtuY1ZsdkIkWldDxA4utq4F6FId1rwIDAQAB',
        'private_key'    => 'MIIEogIBAAKCAQEAhTxCwHFyP3wBe4xVnYeOcnTNDw2SJ2dNAbB6v0NdEXAAZv678PbkZWs6i3GY2NZa3jwCA+xpY1IPia8LhfCRYP92ZotLm1+RG90dAHfTXvbT7Kp6zBL27W6GlHnYbzVz50NBMy8JeNOkhPUoj6eDCqPIHIQmPPqHkKxvTFm6GbeadPlfIL2yl7GjcRFZ+s5G2d74QBk846rGN6sNQhdHSNTFS/27nNiuXdSJYbgrb92UlfnbvdCsn9JGiCLF37tlYgxZZCBxsX6Nsp+gfsnm4TkMpN8wT/A3G5/G5GxuGp5+u8cvr4Phjq3cJXNSYfXXWS9M0dlGIoWKEA+thLIj/QIDAQABAoIBAG06Wf8rAl7Jau2+vvHGkCGFYBdXKNrk9VzNMdRbhZEmIS5O6AVEYzpCDl0DZCtgkF5hE8XjqwKh49schnXoI0dCJ+8pg3J9hpkxTPV1RvQzpsn/eWyUn5tHYU2YFrgVOk/98xXjGgDUgav+0KH00pmGbCf0ruSO/1S8CQKvDMLHc8DLn/7ymfX0F8mvMfoZyvbY9+s27T5JpIr2BgA6Imby1RdZ9fAtroGqhQqPx76nEssFUNjWG4V/WBO8sKMFONYpZTsI+zUfK1FQUEi0xawJsv9/J8hSNw7Brl4DRdB2P34/E1vE63g/Fje8u5mesWSLkVtdYW7VKKkzntvXXAECgYEAwnZ8C/XV0C049NzBjs4K4nMF+6SQLYItho7SyJNWsr8TFJ8+LVQAp7TR0gJXYRNUNCVUZcBaM6tr0h3dTdvlOMptZdJ/A9BwB/Sc/bQCrqiSRYvdigEqeze4O9+JODAagQocLSES5ZfZ0qfc5A0h+o33PjwMKBuNxUTNi5XZQHUCgYEAr2W3QqZ4xC+M5SzACJOgJagWNcO3mZJS2RmzIwhnrTu+DLgcpxbQ3bmCwrUe1eFr3daI9+egTiGOafTE6hCxS7fvLGtx54+C2zNIJ8fbbgo6EerfnR+v7vaxbeYBE6sbKrpdn3lv4eeu6uMyKjjz1D1vTMK1zevfae28Ek6lZGkCgYBFAXaTZ/EvplyHx66H1mXWZarB2WuwOnmdPiFeO6qQU22Z68hMnXp+CJRMSbhoIkvHFVgVo2Re4X2pWh+l+VomBO89Kq0X1Wdr60mqXa51/Cursi4zQqaoguVFmdU/sxI0qDnHae0iu/f34Mlpw59DIg2ScAyATwbH8dpephYp4QKBgDn6FlSasGqxCRKr8yIiohowcH7/Hc5OlsIag6M94P9bMxwDM71rhkj0TCvOMM4kAELI6md/kfahkvClLv3r3J8Zwp5dOb6AKpIIZn53yqmaCc3oZFgMpFwKAWZBU/PLaGR8S/wt6PS0X5ZW2Tsprjdmw0aNSgkyQZxDMBqhEnyZAoGADdO9vl4veB4xbYoMcIkblwIfZp+05QrRPDX9rZ/D7igbW2yitJGueOMz7T2Ad+3yvZ9wH2nUc64EIar/G7NPAjiTOPS6IOlsUGj1ISUhqJdCzDIpMA5t5JF4juv9RWPr0InO3/Bapwsph3JqR3T0Budnu5Ufxijh514vWgpyEqE=',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
