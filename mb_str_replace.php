<?php
/**
 * Multibyte safe version of str_replace.
 * See http://php.net/manual/en/function.str-replace.php
 * @link http://www.php.net/manual/en/ref.mbstring.php#107631
 * @author Michael Robinson <mike@pagesofinterest.net>
 * @author Lee Byron
 */
function mb_str_replace($search, $replace, $subject, $encoding = 'utf8', &$count = null) {

    if (is_array($subject)) {
        $result = array();
        foreach ($subject as $item) {
            $result[] = mb_str_replace($search, $replace, $item, $encoding, $count);
        }
        return $result;
    }

    if (!is_array($search)) {
        return _mb_str_replace($search, $replace, $subject, $encoding, $count);
    }

    $replace_is_array = is_array($replace);
    foreach ($search as $key => $value) {
        $subject = _mb_str_replace($value, $replace_is_array ? $replace[$key] : $replace, $subject, $encoding, $count);
    }
    return $subject;
}

/**
 * Implementation of mb_str_replace. Do not call directly.
 */
function _mb_str_replace($search, $replace,$subject, $encoding, &$count = null) {

    $search_length = mb_strlen($search, $encoding);
    $subject_length = mb_strlen($subject, $encoding);
    $offset = 0;
    $result = '';

    while ($offset < $subject_length) {
        $match = mb_strpos($subject, $search, $offset, $encoding);
        if ($match === false) {
            if ($offset === 0) {
                // No match was ever found, just return the subject.
                return $subject;
            }
            // Append the final portion of the subject to the replaced.
            $result .= mb_substr($subject, $offset, $subject_length - $offset, $encoding);
            break;
        }
        if ($count !== null) {
            $count++;
        }
        $result .= mb_substr($subject, $offset, $match - $offset, $encoding);
        $result .= $replace;
        $offset = $match + $search_length;
    }
    return $result;
}