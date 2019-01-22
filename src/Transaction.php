<?php

// PhpWalletLib
// Copyright (C) 2019 Jittapan Pleumsumran

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

namespace secretdz\phpwalletlib;

class Transaction
{
    /**
     * These are public for easy serialization purpose and for people who are used to using TW API that returns arrays :>
     */
    public $data;
    public $details = null;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param wallet PhpWalletLib object
     * @return TransactionDetails
     */
    public function LoadDetails($wallet)
    {
        if ($this->details === null) {
            $data = $wallet->GetTransactionDetails($this->GetReportId());
            $this->details = new TransactionDetails($data);
        }
        return $this->details;
    }

    /**
     * Get ReportID for further detail inspection
     * @return string report_id
     */
    public function GetReportId()
    {
        return $this->data['report_id'];
    }

    /**
     * Get transaction type
     * @return string
     */
    public function GetType()
    {
        return $this->data['original_type'];
    }

    /**
     * Get action
     * @return string
     */
    public function GetAction()
    {
        return $this->data['original_action'];
    }

    /**
     * Get transaction amount as floating point value
     * @return float
     */
    public function GetAmount()
    {
        return floatval($this->data['amount']);
    }

    /**
     * Get the DateTime object when this transaction happenned
     * @return DateTime
     */
    public function GetDateTime()
    {
        return date_create_from_format('d/m/yy H:i', $this->data['date_time']);
    }
}
