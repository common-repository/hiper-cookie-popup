var site_uri=(window.location.hostname)?window.location.hostname:'';
var hiperCookie = localStorage.getItem(site_uri+"hipercookieconsent");
jQuery(document).ready(function($){
    if(!hiperCookie||hiperCookie!=='true')
    {
        $('.cookie-container').slideDown(200);
    }
    $('#btn-hiper-cookie-consent').on('click',function(e){
        e.preventDefault();
        localStorage.setItem(site_uri+'hipercookieconsent', 'true');
        $('.cookie-container').slideUp(200);
    });
});