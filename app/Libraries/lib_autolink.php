<?php

//
// A PHP auto-linking library
//
// https://github.com/iamcal/lib_autolink
//
// By Cal Henderson <cal@iamcal.com>
// This code is licensed under the MIT license
//

//###################################################################

//
// These are global options. You can set them before calling the autolinking
// functions to change the output.
//

$GLOBALS['autolink_options'] = [
    // Should http:// be visibly stripped from the front
    // of URLs?
    'strip_protocols' => false,
];

//###################################################################

function autolink($text, $limit = 30, $tagfill = '', $auto_title = true)
{
    $text = autolink_do($text, '![a-z][a-z-]+://!i', $limit, $tagfill, $auto_title);
    $text = autolink_do($text, '!(mailto|skype):!i', $limit, $tagfill, $auto_title);

    return autolink_do($text, '!www\\.!i', $limit, $tagfill, $auto_title, 'http://');
}

//###################################################################

function autolink_do($text, $sub, $limit, $tagfill, $auto_title, $force_prefix = null): string
{
    $text_l = mb_strtolower($text);
    $cursor = 0;
    $loop = 1;
    $buffer = '';

    while (($cursor < mb_strlen($text)) && $loop) {
        $ok = 1;
        $matched = preg_match($sub, $text_l, $m, PREG_OFFSET_CAPTURE, $cursor);

        if ( ! $matched) {
            $loop = 0;
            $ok = 0;
        } else {
            $pos = $m[0][1];
            $sub_len = mb_strlen($m[0][0]);

            $pre_hit = mb_substr($text, $cursor, $pos - $cursor);
            $hit = mb_substr($text, $pos, $sub_len);
            $pre = mb_substr($text, 0, $pos);
            $post = mb_substr($text, $pos + $sub_len);

            $fail_text = $pre_hit . $hit;
            $fail_len = mb_strlen($fail_text);

            //
            // substring found - first check to see if we're inside a link tag already...
            //

            $bits = preg_split('!</a>!i', $pre);
            $last_bit = array_pop($bits);
            if (preg_match("!<a\s!i", $last_bit)) {
                //echo "fail 1 at $cursor<br />\n";

                $ok = 0;
                $cursor += $fail_len;
                $buffer .= $fail_text;
            }
        }

        //
        // looks like a nice spot to autolink from - check the pre
        // to see if there was whitespace before this match
        //

        if ($ok !== 0 && $pre) {
            if ( ! preg_match('![\s\(\[\{>]$!s', $pre)) {
                //echo "fail 2 at $cursor ($pre)<br />\n";

                $ok = 0;
                $cursor += $fail_len;
                $buffer .= $fail_text;
            }
        }

        //
        // we want to autolink here - find the extent of the url
        //

        if ($ok !== 0) {
            if (preg_match('/^([a-z0-9\-\.\/\-_%~!?=,:;&+*#@\(\)\$]+)/i', $post, $matches)) {
                $url = $hit . $matches[1];

                $cursor += mb_strlen($url) + mb_strlen($pre_hit);
                $buffer .= $pre_hit;

                $url = html_entity_decode($url);

                //
                // remove trailing punctuation from url
                //

                while (preg_match('|[.,!;:?]$|', $url)) {
                    $url = mb_substr($url, 0, mb_strlen($url) - 1);
                    $cursor--;
                }

                foreach (['()', '[]', '{}'] as $pair) {
                    $o = mb_substr($pair, 0, 1);
                    $c = mb_substr($pair, 1, 1);
                    if (preg_match(sprintf('!^(\%s|^)[^\%s]+\%s$!', $c, $o, $c), $url)) {
                        $url = mb_substr($url, 0, mb_strlen($url) - 1);
                        $cursor--;
                    }
                }

                //
                // nice-i-fy url here
                //

                $link_url = $url;
                $display_url = $url;

                if ($force_prefix) {
                    $link_url = $force_prefix . $link_url;
                }

                if ($GLOBALS['autolink_options']['strip_protocols'] && preg_match('!^(http|https)://!i', $display_url, $m)) {
                    $display_url = mb_substr($display_url, mb_strlen($m[1]) + 3);
                }

                $display_url = autolink_label($display_url, $limit);

                //
                // add the url
                //

                $currentTagfill = $tagfill;
                if ($display_url != $link_url && ! preg_match('@title=@msi', $currentTagfill) && $auto_title) {
                    $display_quoted = preg_quote($display_url, '!');

                    if ( ! preg_match(sprintf('!^(http|https)://%s$!i', $display_quoted), $link_url)) {
                        $currentTagfill .= ' title="' . $link_url . '"';
                    }
                }

                $link_url_enc = htmlspecialchars($link_url);
                $display_url_enc = htmlspecialchars($display_url);

                $buffer .= sprintf('<a href="%s"%s>%s</a>', $link_url_enc, $currentTagfill, $display_url_enc);
            } else {
                //echo "fail 3 at $cursor<br />\n";

                $ok = 0;
                $cursor += $fail_len;
                $buffer .= $fail_text;
            }
        }
    }

    //
    // add everything from the cursor to the end onto the buffer.
    //

    $buffer .= mb_substr($text, $cursor);

    return $buffer;
}

//###################################################################

function autolink_label($text, $limit)
{
    if ( ! $limit) {
        return $text;
    }

    if (mb_strlen($text) > $limit) {
        return mb_substr($text, 0, $limit - 3) . '...';
    }

    return $text;
}

//###################################################################

function autolink_email($text, $tagfill = ''): string
{
    $atom = '[^()<>@,;:\\\\".\\[\\]\\x00-\\x20\\x7f]+'; // from RFC822

    //die($atom);

    $text_l = mb_strtolower($text);
    $cursor = 0;
    $loop = 1;
    $buffer = '';

    while (($cursor < mb_strlen($text)) && $loop) {
        //
        // find an '@' symbol
        //

        $ok = 1;
        $pos = mb_strpos($text_l, '@', $cursor);

        if ($pos === false) {
            $loop = 0;
            $ok = 0;
        } else {
            $pre = mb_substr($text, $cursor, $pos - $cursor);
            $hit = mb_substr($text, $pos, 1);
            $post = mb_substr($text, $pos + 1);

            $fail_text = $pre . $hit;
            $fail_len = mb_strlen($fail_text);

            //die("$pre::$hit::$post::$fail_text");

            //
            // substring found - first check to see if we're inside a link tag already...
            //

            $bits = preg_split('!</a>!i', $pre);
            $last_bit = array_pop($bits);
            if (preg_match("!<a\s!i", $last_bit)) {
                //echo "fail 1 at $cursor<br />\n";

                $ok = 0;
                $cursor += $fail_len;
                $buffer .= $fail_text;
            }
        }

        //
        // check backwards
        //

        if ($ok !== 0) {
            if (preg_match(sprintf('!(%s(\.%s)*)$!', $atom, $atom), $pre, $matches)) {
                // move matched part of address into $hit

                $len = mb_strlen($matches[1]);
                $plen = mb_strlen($pre);

                $hit = mb_substr($pre, $plen - $len) . $hit;
                $pre = mb_substr($pre, 0, $plen - $len);
            } else {
                //echo "fail 2 at $cursor ($pre)<br />\n";

                $ok = 0;
                $cursor += $fail_len;
                $buffer .= $fail_text;
            }
        }

        //
        // check forwards
        //

        if ($ok !== 0) {
            if (preg_match(sprintf('!^(%s(\.%s)*)!', $atom, $atom), $post, $matches)) {
                // move matched part of address into $hit

                $len = mb_strlen($matches[1]);

                $hit .= mb_substr($post, 0, $len);
                $post = mb_substr($post, $len);
            } else {
                //echo "fail 3 at $cursor ($post)<br />\n";

                $ok = 0;
                $cursor += $fail_len;
                $buffer .= $fail_text;
            }
        }

        //
        // commit
        //

        if ($ok !== 0) {
            $cursor += mb_strlen($pre) + mb_strlen($hit);
            $buffer .= $pre;
            $buffer .= sprintf('<a href="mailto:%s"%s>%s</a>', $hit, $tagfill, $hit);
        }
    }

    //
    // add everything from the cursor to the end onto the buffer.
    //

    $buffer .= mb_substr($text, $cursor);

    return $buffer;
}

//###################################################################;
