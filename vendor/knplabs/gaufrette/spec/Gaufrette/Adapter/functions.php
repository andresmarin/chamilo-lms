<?php

namespace Gaufrette\Adapter;

global $createdDirectory;

function ftp_delete($connection, $path)
{
    if ($path === '/home/l3l0/invalid') {
        return false;
    }

    return true;
}

function ftp_mdtm($connection, $path)
{
    if ($path === '/home/l3l0/invalid') {
        return -1;
    }

    return \strtotime('2010-10-10 23:10:10');
}

function ftp_rename($connection, $from, $to)
{
    return ! ('/home/l3l0/invalid' === $from or '/home/l3l0/invalid' === $to);
}

function ftp_fput($connection, $path, $fileResource, $mode)
{
    if ('/home/l3l0/filename' === $path) {
        return true;
    }

    return false;
}

function ftp_fget($connection, &$fileResource, $path, $mode)
{
    if ('/home/l3l0/filename' === $path) {
        $bytes = \fwrite($fileResource, 'some content');

        return true;
    }

    return false;
}

function ftp_chdir($connection, $dirname)
{
    if (in_array($dirname, array('/home/l3l0', '/home/l3l0/aaa', '/home/l3l0/relative', '/home/l3l0/relative/some', '/home/l3l1', 'C:\Ftp'))) {
       return true;
    }

    global $createdDirectory;

    if ($createdDirectory && $createdDirectory === $dirname) {
       return true;
    }

    trigger_error(sprintf('%s: No such file or directory', $dirname), E_USER_WARNING);

    return false;
}

function ftp_mkdir($connection, $dirname)
{
    if (in_array($dirname, array('/home/l3l0/new'))) {
        global $createdDirectory;
        $createdDirectory = $dirname;

        return true;
    }

    return false;
}

function ftp_nlist($connection, $dirname)
{
    $arguments = explode(' ', $dirname);
    switch (end($arguments)) {
        case '/home/l3l0':
            return array('/home/l3l0/filename');
        case '/home/l3l0/aaa':
            return array('/home/l3l0/aaa/filename', '/home/l3l0/aaa/otherFilename');
        case '/home/l3l0/relative':
            return array('filename', 'some');
        case '/home/l3l0/relative/some':
            return array('otherfilename');
    }

    if ('/home/l3l1' === end($arguments)) {
        return array('/home/l3l1/filename', '/home/l3l1/.htaccess');
    }

    return false;
}

function ftp_connect($host, $password)
{
    if ('localhost' !== $host) {
        return false;
    }

    return fopen('php://temp', 'r');
}

function ftp_close($connection)
{
    return fclose($connection);
}

function ftp_rawlist($connection, $directory, $recursive = false)
{
    $arguments = explode(' ', $directory);
    if ('/home/l3l0' === end($arguments))
    {
        return array(
            "drwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 .",
            "drwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 ..",
            "drwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 aaa",
            "-rwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 filename",
            "-rwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 filename.exe",
            "-rwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 .htaccess",
            "lrwxrwxrwx   1 vincent  vincent        11 Jul 12 12:16 www -> aaa"
        );
    }

    if ('/home/l3l0/aaa' === end($arguments))
    {
        return array(
            "-rwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 filename"
        );
    }

    if ('/home/l3l1' === end($arguments) && '-al' === reset($arguments))
    {
        return array(
                "drwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 .",
                "drwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 ..",
                "-rwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 filename",
                "-rwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 .htaccess",
        );
    }

    if ('/home/l3l1' === end($arguments) && '-al' != reset($arguments))
    {
        return array(
                "drwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 .",
                "drwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 ..",
                "-rwxr-x---  15 vincent  vincent      4096 Nov  3 21:31 filename",
        );
    }

    // https://github.com/KnpLabs/Gaufrette/issues/147
    if ('C:\Ftp' === end($arguments))
    {
        return array(
                "05-26-12  08:03PM       <DIR>          archive",
                "12-04-12  06:57PM                16142 file1.zip",
                "12-05-12  04:01PM                16142 file2.zip",
        );
    }

    return array();
}

function ftp_login($connection, $username, $password)
{
    if ('invalid' === $username) {
        return false;
    }

    return true;
}

function file_get_contents($path)
{
    return sprintf('%s content', $path);
}

function time()
{
    return \strtotime('2012-10-10 23:10:10');
}

function file_put_contents($path, $content)
{
    return strlen($content);
}

function rename($from, $to)
{
    return $from.' to '.$to;
}

function file_exists($path)
{
    return in_array($path, array('/home/l3l0/filename', '/home/somedir/filename', 'ssh+ssl://localhost/home/l3l0/filename')) ? true : false;
}

function iterator_to_array($iterator)
{
    global $iteratorToArray;

    return $iteratorToArray;
}
function extension_loaded($name)
{
    global $extensionLoaded;

    if (is_null($extensionLoaded)) {
        return true;
    }

    return $extensionLoaded;
}

function opendir($url)
{
    return true;
}

function filemtime($key)
{
    return 12345;
}

function unlink($key)
{
    return in_array($key, array('/home/l3l0/filename', '/home/somedir/filename')) ? true : false;
}

function is_dir($key)
{
    return (in_array($key, array('/home/l3l0', '/home/l3l0/dir', '/home/somedir', '/home/somedir/dir', '/home/l3l1'))) ? true : false;
}

function realpath($link)
{
    return ('symbolicLink' === $link) ? '/home/somedir' : $link;
}

function is_link($link)
{
    return ('symbolicLink' === $link) ? true : false;
}

function mkdir($directory, $mode, $recursive)
{
    return (in_array($directory, array('/home/other', '/home/somedir/aaa'))) ? true : false;
}

function apc_fetch($path)
{
    return sprintf('%s content', $path);
}

function apc_store($path, $content, $ttl)
{
    if ('prefix-apc-test/invalid' === $path) {
        return false;
    }

    return sprintf('%s content', $path);
}

function apc_delete($path)
{
    if ('prefix-apc-test/invalid' === $path) {
        return false;
    }

    return true;
}

function apc_exists($path)
{
    if ('prefix-apc-test/invalid' === $path) {
        return false;
    }

    return true;
}
