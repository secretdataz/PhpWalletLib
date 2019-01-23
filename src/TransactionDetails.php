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

class TransactionDetails
{
    /**
     * This is public for easy serialization purpose and for people who are used to using TW API that returns arrays :>
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get personal message sent with this transaction
     * @return string
     */
    public function GetMessage()
    {
        return $this->data['personal_message']['value'];
    }

    /**
     * Ref1 may be sender ID, idk
     * @return string
     */
    public function GetRef1()
    {
        return $this->data['ref1'];
    }

    /**
     * Get sender name
     * @return string
     */
    public function GetSenderName()
    {
        return $this->data['section2']['column1']['cell2']['value'];
    }

    /**
     * Get unique transaction reference ID
     * @return string
     */
    public function GetReferenceId()
    {
        return $this->data['section4']['column2']['cell1']['value'];
    }
}
