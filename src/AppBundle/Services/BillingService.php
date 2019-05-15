<?php

namespace AppBundle\Services;

/**
 * Class BillingService
 * @package AppBundle\Services
 *
 * Handles how features are mapped to billing plans
 *
 */
class BillingService
{
    /** @var string */
    private $env;

    /**
     * @param $symfonyEnv
     */
    public function __construct($symfonyEnv)
    {
        $this->env = $symfonyEnv;
    }

    public function isEnabled($plan = 'plus', $feature)
    {

        // For trial accounts
        if (!$plan) {
            $plan = 'plus';
        }

        $enabled = [
            'CheckInPrompt'     => false,
            'CheckOutPrompt'    => false,
            'ProductField'      => false,
            'ContactField'      => false,
            'ItemAttachment'    => false,
            'ContactAttachment' => false,
            'Deposits'          => false,
            'CustomEmail'       => false,
            'Site'              => false,
            'Page'              => false,
            'PrivateSite'       => false,
            'CustomStyle'       => false,
            'CustomTheme'       => false,
            'MultipleLanguages' => false,
            'EmailAutomation'   => false,
            'Labels'            => false,
            'WhiteLabel'        => false,
        ];

        switch ($plan) {

            case 'free':
                // nothing extra for the free plan
                break;

            case 'starter':
                $enabled = [
                    'CheckInPrompt'     => false,
                    'CheckOutPrompt'    => false,
                    'ProductField'      => false,
                    'ContactField'      => false,
                    'ItemAttachment'    => false,
                    'ContactAttachment' => false,
                    'Deposits'          => true,
                    'CustomEmail'       => true,
                    'Site'              => false,
                    'Page'              => true,
                    'PrivateSite'       => true,
                    'CustomStyle'       => true,
                    'CustomTheme'       => false,
                    'MultipleLanguages' => true,
                    'EmailAutomation'   => true,
                    'Labels'            => false,
                    'WhiteLabel'        => false,
                ];
                break;

            case 'plus':
                $enabled = [
                    'CheckInPrompt'     => true,
                    'CheckOutPrompt'    => true,
                    'ProductField'      => true,
                    'ContactField'      => true,
                    'ItemAttachment'    => true,
                    'ContactAttachment' => true,
                    'Deposits'          => true,
                    'CustomEmail'       => true,
                    'Site'              => true,
                    'Page'              => true,
                    'PrivateSite'       => true,
                    'CustomStyle'       => true,
                    'CustomTheme'       => true,
                    'MultipleLanguages' => false,
                    'EmailAutomation'   => true,
                    'Labels'            => true,
                    'WhiteLabel'        => false,
                ];
                break;

            case 'business':
                $enabled = [
                    'CheckInPrompt'     => true,
                    'CheckOutPrompt'    => true,
                    'ProductField'      => true,
                    'ContactField'      => true,
                    'ItemAttachment'    => true,
                    'ContactAttachment' => true,
                    'Deposits'          => true,
                    'CustomEmail'       => true,
                    'Site'              => true,
                    'Page'              => true,
                    'PrivateSite'       => true,
                    'CustomStyle'       => true,
                    'CustomTheme'       => true,
                    'MultipleLanguages' => true,
                    'EmailAutomation'   => true,
                    'Labels'            => true,
                    'WhiteLabel'        => true,
                ];
                break;

        }

        if (isset($enabled[$feature]) && $enabled[$feature] == false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $plan
     * @return int
     */
    public function getMaxItems($plan)
    {
        // For trial accounts
        if (!$plan) {
            $plan = 'free';
        }

        switch ($plan) {
            case 'free':
                return 100;
                break;

            case 'starter':
                return 500;
                break;

            case 'plus':
                return 2000;
                break;

            case 'business':
                return 10000;
                break;
        }

        return 0;
    }

    /**
     * @param $plan
     * @return int
     */
    public function getMaxContacts($plan)
    {

        if (!$plan) {
            $plan = 'free';
        }

        switch ($plan) {
            case 'free':
                return 100;
                break;

            case 'starter':
                return 500;
                break;

            case 'plus':
                return 2000;
                break;

            case 'business':
                return 10000;
                break;
        }

        return 0;
    }

    /**
     * @param $plan
     * @return int
     */
    public function getMaxSites($plan)
    {

        // During trial, only one site
        if (!$plan) {
            $plan = 'free';
        }

        switch ($plan) {
            case 'free':
                return 1;
                break;

            case 'starter':
                return 1;
                break;

            case 'plus':
                return 5;
                break;

            case 'business':
                return 20;
                break;
        }

        return 0;
    }

    /**
     * Returns the CURRENT billing plans for display in the UI
     * Customers on legacy plans are mapped to one of the current plans in CustomConnectionFactory
     * @return array
     */
    public function getPlans()
    {

        if ($this->env == 'prod') {

            // ALL PROD SERVERS

            $plans = [
                [
                    'code' => 'free',
                    'stripeCode' => 'free',
                    'name' => 'Free',
                    'amount' => 0
                ],
                [
                    'code' => 'starter',
                    'stripeCode' => 'plan_Cv8Lg7fyOJSB0z', // Standard monthly 5.00
                    'name' => 'Starter',
                    'amount' => 500
                ],
                [
                    'code' => 'plus',
                    'stripeCode' => 'plus',
                    'name' => 'Plus',
                    'amount' => 2000
                ],
                [
                    'code' => 'business',
                    'stripeCode' => 'plan_F4HgQehPQ2nOlN',
                    'name' => 'Business',
                    'amount' => 4000
                ]
            ];

        } else {

            // STAGING AND DEV SERVER

            $plans = [
                [
                    'code' => 'free',
                    'stripeCode' => 'free',
                    'name' => 'Free',
                    'amount' => 0
                ],
                [
                    'code' => 'starter',
                    'stripeCode' => 'plan_Cv6rBge0LPVNin',
                    'name' => 'Starter',
                    'amount' => 500
                ],
                [
                    'code' => 'plus',
                    'stripeCode' => 'plus',
                    'name' => 'Plus',
                    'amount' => 2000
                ],
                [
                    'code' => 'business',
                    'stripeCode' => 'plan_F4HR4VG76biNcB',
                    'name' => 'Business',
                    'amount' => 4000
                ]
            ];

        }

        return $plans;
    }

    /**
     * Transform plan_Cv6rBge0LPVNin to starter to allow dynamic plans on Stripe while keeping fixed codes in app
     * @param $planStripeCode
     * @return mixed
     */
    public function getPlanCode($planStripeCode)
    {
        $plan = 'NOTSET';
        switch ($planStripeCode) {
            case 'free':
                $plan = 'free';
                break;
            case 'standard':
            case 'starter':
            case 'plan_Cv8Lg7fyOJSB0z': // standard monthly 5.00
            case 'plan_Cv6TbQ0PPSnhyL': // test plan
            case 'plan_Cv6rBge0LPVNin': // test plan
            case 'single':
                $plan = 'starter';
                break;
            case 'premium':
            case 'plus':
            case 'multiple':
                $plan = 'plus';
                break;
            case 'business':
            case 'plan_F4HR4VG76biNcB': // test
            case 'plan_F4HgQehPQ2nOlN': // prod
                $plan = 'business';
                break;
        }

        return $plan;
    }
}