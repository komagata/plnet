<?php
if (!defined(LOCALE_DETECT)) {
    define('LOCALE_DETECT', true);
}
if (!defined(DEFAULT_LOCALE)) {
    define('DEFAULT_LOCALE', 'en');
}

class MessageFilter extends Filter
{
	function execute(&$filterChain, &$controller, &$request, &$user)
    {
        $locale = $user->hasAttribute('locale', 'net.mojavi.locale')
            ? $user->getAttribute('locale', 'net.mojavi.locale') : DEFAULT_LOCALE;
        
        if (LOCALE_DETECT) {
            $locale = MessageFilter::getLocale();
            if (preg_match('/[_-]/', $locale)) {
                list($locale, $country) = split("[_-]", $locale);
            }
        }

        // locale for session
        $member = $user->hasAttribute('member', GLU_NS) 
            ? $user->getAttribute('member', GLU_NS) : null;
        if (isset($member->language) and strlen($member->language) > 0) {
            $locale = $member->language;
        }

		if ($request->hasAttribute('messages') === false) {
            $request->setAttribute('locale', $locale);
            if (!is_readable(BASE_DIR."locales/$locale.ini")) {
                $messages = parse_ini_file(BASE_DIR.'locales/'.DEFAULT_LOCALE.'.ini');
            } else {
                $messages = parse_ini_file(BASE_DIR."locales/$locale.ini");
            }
			
			$request->setAttribute('messages', $messages);
			$filterChain->execute($controller, $request, $user);
		} else {
			$filterChain->execute($controller, $request, $user);
		}
	}
	
	function getLocale()
    {
        $locale = array_shift(MessageFilter::getLocales());
		return empty($locale) ? DEFAULT_LOCALE : $locale;
	}

	function getLocales()
    {
		foreach (array_reverse(split(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"])) as $value) {
			list($name, $pri) = split(";", trim($value));
			$num = preg_replace("/^q=/", "", $pri);
			$langs[$num ? $num : 1] = $name;
		}
		arsort($langs, SORT_NUMERIC);
		foreach ($langs as $value) {
			$locales[] = $value;
		}
		return $locales;
	}
}
?>
