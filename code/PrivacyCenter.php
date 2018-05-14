<?php

class PrivacyCenterExtension extends DataExtension
{

    /**
     * On After init, based on config, include CookiePolicy items.
     */
    public function onAfterInit()
    {
            Requirements::javascript('privacycenter/js/min/main.js');
            Requirements::css('privacycenter/css/main.css');

            // Always include GTM. Scripts and events are fired based on cookie settings.
            //$this->includeGTM();
    }

    public function PrivacyCenter()
    {
        $config = Config::inst();

        $strictlyCookies = array();
        foreach ($config->get('PrivacyCenter', 'StrictlyCookies') as $item) {
            array_push($strictlyCookies, ArrayData::create(['Text' => $item]));
        }

        $performanceCookies = array();
        foreach ($config->get('PrivacyCenter', 'PerformanceCookies') as $item) {
            array_push($performanceCookies, ArrayData::create(['Text' => $item]));
        }

        $functionalCookies = array();
        foreach ($config->get('PrivacyCenter', 'FunctionalCookies') as $item) {
            array_push($functionalCookies, ArrayData::create(['Text' => $item]));
        }

        $targetingCookies = array();
        foreach ($config->get('PrivacyCenter', 'TargetingCookies') as $item) {
            array_push($targetingCookies, ArrayData::create(['Text' => $item]));
        }

        $GoogleTagID = $config->get('PrivacyCenter', 'TagID');

        return $this->owner->customise(array(
            'StrictlyCookies' => ArrayList::create($strictlyCookies),
            'PerformanceCookies' => ArrayList::create($performanceCookies),
            'FunctionalCookies' => ArrayList::create($functionalCookies),
            'TargetingCookies' => ArrayList::create($targetingCookies),
            'TagID' => $GoogleTagID[0]
        ))->renderWith('PrivacyCenter');
    }

    public function CookiePopup(){
        if(!$this->accepted()){
            $privacysnippet = new ArrayData([]);
            $page = $this->owner->customise(array('CookiePopup' => $privacysnippet));
            return $page->renderWith('CookiePopup');
        }
    }

    protected function includeGTM()
    {
        $config = Config::inst();
        $GoogleTagID = $config->get('PrivacyCenter', 'TagID');

        // Inject GTM script
        Requirements::insertHeadTags("<!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','".$GoogleTagID[0]."');</script>
            <!-- End Google Tag Manager -->
            ");

    }

    /**
     * @return bool
     */
    public static function accepted()
    {
        // must check for string values, using filter_var.
        return filter_var(Cookie::get('cookie_decided'), FILTER_VALIDATE_BOOLEAN);
    }

}