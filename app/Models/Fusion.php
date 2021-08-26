<?php

namespace App\Models;

use ErrorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fusion extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_holder_id',
        'account_id',
        'user_id',
        'account_holder_details',
        'issue_bundle_details'
    ];

    /**
     * @param $userId
     * @return bool
     * @throws ErrorException
     */
    public static function createAccountHolder($userId): bool
    {
        $userInformation = UserInformation::getUserDataForFusion($userId);
        $data = [
            'ifiID' => config('finrant.fusion_ifi_id'),
            'individualType' => 'REAL',
            'formID' => 'finrant-' . Str::random(30),
            'firstName' => $userInformation['firstName'],
            'middleName' => '',
            'lastName' => $userInformation['lastName'],
            'applicationType' => 'CREATE_ACCOUNT_HOLDER',
            'dob' => [
                'year' => $userInformation['dob_year'],
                'month' => $userInformation['dob_month'],
                'day' => $userInformation['dob_day'],
            ],
            'gender' => $userInformation['gender'],
            'kycDetails' => [
                'kycStatus' => 'MINIMAL',
                'authData' => [
                    'PAN' => $userInformation['pan']
                ],
                'authType' => 'PAN'
            ],
            'vectors' => [
                [
                    'type' => 'p',
                    'value' => $userInformation['phoneNumber'],
                    'isVerified' => false,
                ]
            ]
        ];
        $ifiId = config('finrant.fusion_ifi_id');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fusion.preprod.zeta.in/api/v1/ifi/$ifiId/applications/newIndividual",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Zeta-AuthToken: ' . config('finrant.fusion_auth_token')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (empty($response)) {
            throw new ErrorException('Failed to create fusion account holder');
        }

        $responseArray = json_decode($response,true);

        if ($responseArray['status'] !== 'APPROVED') {
            throw new ErrorException((empty($responseArray['statusDetails']) || !array_key_exists('message', $responseArray['statusDetails'])) ?
                'Failed to create fusion account holder.' :
                $responseArray['statusDetails']['message']
            );
        }
        $fusionAccount = new Fusion();
        $fusionAccount->user_id = $userId;
        $fusionAccount->account_holder_details = $response;
        $fusionAccount->account_holder_id = $responseArray['individualID'];
        return $fusionAccount->save();
    }

    public static function getUserAccountHolderId($userId): string
    {
        $result = self::where('user_id', $userId)->get()->toArray();
        return (empty($result) || empty($result[0]['account_holder_id'])) ? '' : $result[0]['account_holder_id'];
    }

    public static function getUserAccountId($userId): string
    {
        $result = self::where('user_id', $userId)->get()->toArray();
        return (empty($result) || empty($result[0]['account_id'])) ? '' : $result[0]['account_id'];
    }

    /**
     * @param $userId
     * @return bool
     * @throws ErrorException
     */
    public static function issueBundle($userId): bool
    {
        $userInformation = UserInformation::getUserDataForFusion($userId);
        $accountHolderID = self::getUserAccountHolderId($userId);
        $bundleName = config('finrant.fusion_bundle_name');
        $bundleId = config('finrant.fusion_bundle_id');
        $ifiId = config('finrant.fusion_ifi_id');
        $curl = curl_init();
        $data = [
            'accountHolderID' => $accountHolderID,
            'name' => $bundleName,
            'phoneNumber' => $userInformation['phoneNumber']
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fusion.preprod.zeta.in/api/v1/ifi/$ifiId/bundles/$bundleId/issueBundle",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Zeta-AuthToken: ' . config('finrant.fusion_auth_token')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (empty($response)) {
            throw new ErrorException('Failed to create fusion account holder');
        }
        $responseArray = json_decode($response,true);
        $fusionAccount = self::where('user_id', $userId)->first();
        $fusionAccount->account_id = $responseArray['accounts'][0]['accountID'];
        return $fusionAccount->save();
    }

    /**
     * @param $userId
     * @param $amount
     * @return array
     * @throws ErrorException
     */
    public static function transferFundsToUser($userId, $amount): array
    {
        $fundingAccountId = config('finrant.fusion_funding_ac_id');
        $transactionUniqueKey = 'finrant-' . Str::random(30);
        $data = [
            'requestID' => $transactionUniqueKey,
            'amount' => [
                'currency' => 'INR',
                'amount' => $amount
            ],
            'transferCode' => 'A2A_VBOPayout-VBO2U_AUTH',
            'debitAccountID' => $fundingAccountId,
            'creditAccountID' => self::getUserAccountId($userId),
            'transferTime' => time(),
            'remarks' => "Transferring to user_id $userId"
        ];
        $curl = curl_init();
        $ifiId = config('finrant.fusion_ifi_id');
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fusion.preprod.zeta.in/api/v1/ifi/$ifiId/transfers",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Zeta-AuthToken: eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwidGFnIjoidllsMkxES0hZRTQxaHVfb1hiM0NwUSIsImFsZyI6IkExMjhHQ01LVyIsIml2IjoiZFNidTBUUVVBZ2NEczhpYSJ9.eCgX6lEZq7TxNYtFkvepigHw8BBIcUbW711JPVAGI4c.L_ekBI7ow-c9OHGFJJ1LGw.1xGOTyj5vAmHJaFlCud_Y5Qzh98KHcKAwlzf30QPu5IYTxooE7RPstovO5yTLe9seIsJrKzZOHT3wgt9d13I196gJAx7J6KV0axV6UFx-bxWh6qxCZtIYCYcnk0XOPO6fQKU_81Y1AQgJUIfw2z03iMPBP-A5t-Kidh0CSUqsNJ15CRthwZdayTaq8FYJ8B9hMGJxsS1l8wuqRyvIWwrxdGzvhaGW3_HxIKK7gOT4ljJ-Yeq51p1UIDHNpchhnVPrvIWQxcLiUjb0NmRuTDAmAsLz_KL4Jg8tZGwjnwqfAh2IDJRmYIrrijPX_YnC6Xlzv6TcGgp5120SdviWwqhjq6vMhfCo1jPDjXSnVp83lC3S-hXSjPAfmUNpqm698BK.kcvOICcfz6uyDzYMlFIc2Q'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (empty($response)) {
            throw new ErrorException('Fund transfer failed.');
        }

        $responseArray = json_decode($response, true);
        logger($response);
        if (empty($responseArray['status']) || $responseArray['status'] !== 'SUCCESS') {
            throw new ErrorException('Fund transfer failed.');
        }

        return [
            $transactionUniqueKey,
            $responseArray['transferID']
        ];
    }
}
