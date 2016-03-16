<?php

namespace Furniture\FrontendBundle\Util;

class UrlFormatter
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $originalUrl;
    /**
     * @var array
     */
    private $parsedUrl;

    /**
     * @var string
     */
    private $formatedUrl;

    /**
     * @var string
     */
    private $defaultScheme = 'http';

    /**
     * @var bool
     */
    private $urlValid = false;

    /**
     * @var string
     */
    private $parsePattern = "@(aaas?|about|acap|acct|cap|coaps?|cr?id|data|dav|dict|dns|example|fax|file|filesystem|ge?o|gopher|h323|iax|icap|im|imap|info|ipps?|iris|iris\.beep|iris\.lwz|iris\.xpcs?|jabber|ldap|mailserver|mailto|mid|modem|msrps?|mtqp|mupdate|news|nfs|nih?|nntp|opaquelocktoken|pack|pkcs11|pop|pres|prospero|reload|rtsps?u? service|session|s?https?|sieve|sips?|sms|snews|snmp|soap\.beep|soap\.beeps|stuns?|tag|tel|telnet|t?ftp|thismessage|tip|tn3270|turns?|tv|urn|vemmi|videotex|wais|wss?|xcon|xcon-userid|xmlrpc\.beep|xmlrpc\.beeps|xmpp|z39\.50r?s?)?(://)?(([\w-~!*'();=+$,\[\]]+)?(:)?([\w-~!*'();=+$,\[\]]+)?\@)?([\w-.~!*'();=+$,\[\]]+)?(:)?(\d+)?([\/\w-.~!*'();=+$,\[\]]+)?(\?)?([\/\w-.~!*'();=+$&,\[\]%]+)?(#)?([\/\w-.~!*'();=+$,\[\]]+)?@";

    /**
     * @var array
     */
    private $parts;

    /**
     * @var string
     */
    private $formatter;

    /**
     * @var bool
     */
    private $isSingle;

    /**
     * @var array
     */
    private $allowedParts = [
        'scheme',
        'user',
        'pass',
        'host',
        'port',
        'path',
        'query',
        'fragment',
    ];

    /**
     * @param string $url
     */
    public function __construct($url = '')
    {
        if (!empty($url)) {
            $this->setUrl($url);
        }
    }

    /**
     * @param array $parts
     * @return string
     */
    public function format(array $parts = [])
    {
        $this->setParts($parts);
        if ($this->urlIsValid()) {
            $this->parseUrl();
            $this->createFormatter();
            $this->replaceComponents();
            $this->clearFormattedUrl();

            return $this->formatedUrl;
        } else {
            return $this->originalUrl;
        }
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->originalUrl = $url;
        $this->fixUrlScheme();
        $this->urlValid = !!filter_var($this->url, FILTER_VALIDATE_URL);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * @param $originalUrl
     * @return $this
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }


    /**
     * @param $parsePattern
     * @return $this
     */
    public function setParsePattern($parsePattern)
    {
        $this->parsePattern = $parsePattern;

        return $this;
    }

    /**
     * @return string
     */
    public function getParsePattern()
    {
        return $this->parsePattern;
    }


    /**
     * @param $defaultScheme
     * @return $this
     */
    public function setDefaultScheme($defaultScheme)
    {
        $this->defaultScheme = $defaultScheme;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultScheme()
    {
        return $this->defaultScheme;
    }


    /**
     * Parse url with RegExp pattern.
     */
    public function parseUrl()
    {
        preg_match($this->parsePattern, $this->url, $matches);
        if ($matches[0]) {
            $this->parsedUrl = $this->matchComponents($matches);
        }
    }

    /**
     * @return bool
     */
    public function urlIsValid()
    {
        return $this->urlValid;
    }

    /**
     * @param array $parts
     *
     * @return $this
     */
    public function setParts(array $parts)
    {
        if (empty($this->parts)) {
            $this->parts = empty($parts) ? ['scheme', 'host', 'path'] : $parts;
        } else {
            $this->parts += $parts;
        }

        $this->checkSingle();

        return $this;
    }

    /**
     * Check URL scheme in provided URL string and fix it.
     */
    private function fixUrlScheme()
    {
        $schemePattern = '@^(aaas?|about|acap|acct|cap|coaps?|cr?id|data|dav|dict|dns|example|fax|file|filesystem|ge?o|gopher|h323|iax|icap|im|imap|info|ipps?|iris|iris\.beep|iris\.lwz|iris\.xpcs?|jabber|ldap|mailserver|mailto|mid|modem|msrps?|mtqp|mupdate|news|nfs|nih?|nntp|opaquelocktoken|pack|pkcs11|pop|pres|prospero|reload|rtsps?u? service|session|s?https?|sieve|sips?|sms|snews|snmp|soap\.beep|soap\.beeps|stuns?|tag|tel|telnet|t?ftp|thismessage|tip|tn3270|turns?|tv|urn|vemmi|videotex|wais|wss?|xcon|xcon-userid|xmlrpc\.beep|xmlrpc\.beeps|xmpp|z39\.50r?s?)?((:)?//)?@';
        preg_match($schemePattern, $this->url, $matches);

        if ($matches[0]) {
            if (empty($matches[1])) {
                $matches[1] = $this->defaultScheme;
            }
            if (!empty($matches[2]) && empty($matches[3])) {
                $matches[2] = ':'.$matches[2];
            }
            preg_replace($schemePattern, $matches[1].$matches[2], $this->url);
        } else {
            $this->url = $this->defaultScheme.'://'.$this->url;
        }
    }

    /**
     * Replace all components in formatter.
     */
    private function replaceComponents()
    {
        $patterns = array_map(
            function ($n) {
                return sprintf(':%s:', $n);
            },
            array_keys($this->parsedUrl)
        );

        $replaces = array_values($this->parsedUrl);

        $this->formatedUrl = str_replace($patterns, $replaces, $this->formatter);
    }

    /**
     * Clear formatted url from formatting patterns.
     */
    private function clearFormattedUrl()
    {
        $diff = array_diff($this->parts, array_keys($this->parsedUrl));

        if (!empty($diff)) {
            $replaces = array_map(
                function ($n) {
                    return $this->getFormat($n);
                },
                $diff
            );
            $this->formatedUrl = str_replace($replaces, '', $this->formatedUrl);
        }
    }

    /**
     * @param array $matches
     * @return array
     */
    private function matchComponents(array $matches)
    {
        $matched = [];
        foreach ([1, 3, 5, 7, 9, 10, 12, 14] as $pos) {
            if (isset($matches[$pos])) {
                $matched[] = $matches[$pos];
            }
        }

        $allowed = array_slice($this->allowedParts, 0, count($matched));
        $combine = array_combine($allowed, $matched);
        $this->checkParsedScheme($combine);

        return array_filter($combine);
    }

    /**
     * @param array $parsed
     */
    private function checkParsedScheme(array &$parsed)
    {
        if (isset($parsed['scheme']) && empty($parsed['scheme'])) {
            $parsed['scheme'] = $this->defaultScheme;
        }
    }

    /**
     * Check that return format is single part or not.
     */
    private function checkSingle()
    {
        $this->isSingle = count($this->parts) == 1;
    }

    /**
     * Create formatter string.
     */
    private function createFormatter()
    {
        $this->formatter = '';
        if ($this->hasComponent('scheme')) {
            $this->formatter .= $this->getSchemeFormat();
        }

        if ($this->hasComponent('user')) {
            $this->formatter .= $this->getUserFormat();
        }

        if ($this->hasComponent('pass')) {
            $this->formatter .= $this->getPasswordFormat();
        }

        if (!$this->isSingle && $this->hasComponent('user')) {
            $this->formatter .= '@';
        }

        if ($this->hasComponent('host')) {
            $this->formatter .= $this->getHostFormat();
        }

        if ($this->hasComponent('port')) {
            $this->formatter .= $this->getPortFormat();
        }

        if ($this->hasComponent('path')) {
            $this->formatter .= $this->getPathFormat();
        }

        if ($this->hasComponent('query')) {
            $this->formatter .= $this->getQueryFormat();
        }

        if ($this->hasComponent('fragment')) {
            $this->formatter .= $this->getFragmentFormat();
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    private function hasComponent($name)
    {
        return in_array($name, $this->parts);
    }

    /**
     * @param string $name
     * @return string
     */
    private function getFormat($name)
    {
        if (!empty($name)) {
            $method = 'get'.ucfirst($name).'Format';
            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        return '';
    }

    /**
     * @return string
     */
    private function getSchemeFormat()
    {
        return !$this->isSingle ? ':scheme:://' : ':scheme:';
    }

    /**
     * @return string
     */
    private function getUserFormat()
    {
        return ':user:';
    }

    /**
     * @return string
     */
    private function getPasswordFormat()
    {
        if (!$this->isSingle && $this->hasComponent('user')) {
            return '::pass:';
        } else if ($this->isSingle) {
            return ':pass:';
        }

        return '';
    }

    /**
     * @return string
     */
    private function getHostFormat()
    {
        return ':host:';
    }

    /**
     * @return string
     */
    private function getPortFormat()
    {
        return !$this->isSingle ? '::port:' : ':port:';
    }

    /**
     * @return string
     */
    private function getPathFormat()
    {
        return ':path:';
    }

    /**
     * @return string
     */
    private function getQueryFormat()
    {
        return !$this->isSingle ? '?:query:' : ':query:';
    }

    /**
     * @return string
     */
    private function getFragmentFormat()
    {
        return !$this->isSingle ? '#:fragment:' : ':fragment:';
    }
}