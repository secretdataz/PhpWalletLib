# PhpWalletLib
PhpWalletLib is an unofficial PHP library for connecting to TrueMoney Wallet API.

## Usage
with composer,
```
composer require secretdz/php-wallet-lib
```

## Example
Using PhpWalletLib is easy. It does most of the stuff under the hood.
```php
<?php
require 'vendor/autoload.php';

use secretdz\phpwalletlib\PhpWalletLib;

// Log in
$wallet = new PhpWalletLib('a@a.com', 'hunter2', 'email'); // Can also specify mobile instead of email for username and type.

// Dump profile
var_dump($wallet->GetProfile());

// Get current balance
echo 'Balance: ' . $wallet->GetBalance();

// Top up TrueMoney cash card to this account
if ($wallet->TopupCashCard('012345678901234')) {
    echo 'Topup complete!';
} else {
    echo 'Topup failed!';
}

// Get last 30 transaction from yesterday and print all income
// Check Transaction.php and TransactionDetails.php to see what this wrapper covers.
// PhpWalletLib parameter of Transaction::LoadDetails can be omitted after the first call.
// Or you can just store the TransactionDetails returned from it in a variable :)
$start = date('Y-m-d', strtotime('-1 days'));
$end = date('Y-m-d', strtotime('1 days'));
$txs = $wallet->GetPastTransactions($start, $end, 30)->transactions;
foreach ($txs as $tx) {
    if ($tx->GetAction() === 'creditor') {
        $details = $tx->LoadDetails($wallet);
        echo '[' . $tx->GetDateTime()->format('Y-m-d H:i') . '] Transaction from ' . $details->GetSenderName() . ' with amount of ' . $tx->GetAmount() . ' with message "' . $details->GetMessage() . '" <br>';
    }
}

```

## License
This library is licensed under the GNU GPLv3 license.
Copyright (C) 2019 Jittapan Pleumsumran.

## Disclaimer
PhpWalletLib isn't endorsed by True Money Co.,Ltd. and doesn't reflect the views or opinions of them or anyone officially involved.  
TrueMoney is a trademark of TrueMoney Co.,Ltd.
