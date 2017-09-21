function goLinkDelete(link,mess){
    $conf = confirm(mess);
    if ($conf) {
        window.location = link;
    }
}