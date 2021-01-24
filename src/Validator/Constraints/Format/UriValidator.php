<?php declare(strict_types=1);

namespace OAS\Validator\Constraints\Format;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UriValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$this->matches($value)) {
            $this->context
                ->buildViolation(Uri::INVALID_URI_MESSAGE)
                ->setCode(Uri::INVALID_URI_ERROR)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }

    private function matches(string $uri): bool
    {
        // credits: https://gist.github.com/kpobococ/92f120c6c4a9a52b84e3

        // Links to relevant RFC documents:
        // RFC 3986: http://tools.ietf.org/html/rfc3986 (URI scheme)
        // RFC 2234: http://tools.ietf.org/html/rfc2234#section-6.1 (ABNF notation)
        $regex = '/
          # URI scheme RFC 3986
       
          (?(DEFINE)
        
          # ABNF notation of RFC 2234
            
          (?<ALPHA>     [\x41-\x5A\x61-\x7A] )    # Latin character (A-Z, a-z)
          (?<CR>        \x0D )                    # Carriage return (\r)
          (?<DIGIT>     [\x30-\x39] )             # Decimal number (0-9)
          (?<DQUOTE>    \x22 )                    # Double quote (")
          (?<HEXDIG>    (?&DIGIT) | [\x41-\x46] ) # Hexadecimal number (0-9, A-F)
          (?<LF>        \x0A )                    # Line feed (\n)
          (?<SP>        \x20 )                    # Space
        
          # RFC 3986 body
        
          (?<uri>    (?&scheme) \: (?&hier_part) (?: \? (?&query) )? (?: \# (?&fragment) )? )
        
          (?<hier_part>    \/\/ (?&authority) (?&path_abempty)
                         | (?&path_absolute)
                         | (?&path_rootless)
                         | (?&path_empty) )
        
          (?<uri_reference>    (?&uri) | (?&relative_ref) )
        
          (?<absolute_uri>    (?&scheme) \: (?&hier_part) (?: \? (?&query) )? )
        
          (?<relative_ref>    (?&relative_part) (?: \? (?&query) )? (?: \# (?&fragment) )? )
        
          (?<relative_part>     \/\/ (?&authority) (?&path_abempty)
                              | (?&path_absolute)
                              | (?&path_noscheme)
                              | (?&path_empty) )
        
          (?<scheme>    (?&ALPHA) (?: (?&ALPHA) | (?&DIGIT) | \+ | \- | \. )* )
        
          (?<authority>    (?: (?&userinfo) \@ )? (?&host) (?: \: (?&port) )? )
          (?<userinfo>     (?: (?&unreserved) | (?&pct_encoded) | (?&sub_delims) | \: )* )
          (?<host>         (?&ip_literal) | (?&ipv4_address) | (?&reg_name) )
          (?<port>         (?&DIGIT)* )
        
          (?<ip_literal>    \[ (?: (?&ipv6_address) | (?&ipv_future) ) \] )
        
          (?<ipv_future>    \x76 (?&HEXDIG)+ \. (?: (?&unreserved) | (?&sub_delims) | \: )+ )
        
          (?<ipv6_address>                                              (?: (?&h16) \: ){6} (?&ls32)
                            |                                      \:\: (?: (?&h16) \: ){5} (?&ls32)
                            |                           (?&h16)?   \:\: (?: (?&h16) \: ){4} (?&ls32)
                            | (?: (?: (?&h16) \: ){0,1} (?&h16) )? \:\: (?: (?&h16) \: ){3} (?&ls32)
                            | (?: (?: (?&h16) \: ){0,2} (?&h16) )? \:\: (?: (?&h16) \: ){2} (?&ls32)
                            | (?: (?: (?&h16) \: ){0,3} (?&h16) )? \:\:     (?&h16) \:      (?&ls32)
                            | (?: (?: (?&h16) \: ){0,4} (?&h16) )? \:\:                     (?&ls32)
                            | (?: (?: (?&h16) \: ){0,5} (?&h16) )? \:\:                     (?&h16)
                            | (?: (?: (?&h16) \: ){0,6} (?&h16) )? \:\: )
        
          (?<h16>             (?&HEXDIG){1,4} )
          (?<ls32>            (?: (?&h16) \: (?&h16) ) | (?&ipv4_address) )
          (?<ipv4_address>    (?&dec_octet) \. (?&dec_octet) \. (?&dec_octet) \. (?&dec_octet) )
        
          (?<dec_octet>    (?&DIGIT)
                         | [\x31-\x39] (?&DIGIT)
                         | \x31 (?&DIGIT){2}
                         | \x32 [\x30-\x34] (?&DIGIT)
                         | \x32\x35 [\x30-\x35] )
        
          (?<reg_name>     (?: (?&unreserved) | (?&pct_encoded) | (?&sub_delims) )* )
        
          (?<path>    (?&path_abempty)
                    | (?&path_absolute)
                    | (?&path_noscheme)
                    | (?&path_rootless)
                    | (?&path_empty) )
        
          (?<path_abempty>     (?: \/ (?&segment) )* )
          (?<path_absolute>    \/ (?: (?&segment_nz) (?: \/ (?&segment) )* )? )
          (?<path_noscheme>    (?&segment_nz_nc) (?: \/ (?&segment) )* )
          (?<path_rootless>    (?&segment_nz) (?: \/ (?&segment) )* )
          (?<path_empty>       (?&pchar){0} ) # For explicity only
        
          (?<segment>       (?&pchar)* )
          (?<segment_nz>    (?&pchar)+ )
          (?<segment_nz_nc> (?: (?&unreserved) | (?&pct_encoded) | (?&sub_delims) | \@ )+ )
        
          (?<pchar>    (?&unreserved) | (?&pct_encoded) | (?&sub_delims) | \: | \@ )
        
          (?<query>    (?: (?&pchar) | \/ | \? )* )
        
          (?<fragment>    (?: (?&pchar) | \/ | \? )* )
        
          (?<pct_encoded>    \% (?&HEXDIG) (?&HEXDIG) )
        
          (?<unreserved>    (?&ALPHA) | (?&DIGIT) | \- | \. | \_ | \~ )
          (?<reserved>      (?&gen_delims) | (?&sub_delims) )
          (?<gen_delims>    \: | \/ | \? | \# | \[ | \] | \@ )
          (?<sub_delims>    \! | \$ | \& | \' | \( | \)
                          | \* | \+ | \, | \; | \= )
        
        )
    
        ^(?&uri)$
       
        /x';

        return 1 === preg_match($regex, $uri);
    }
}
