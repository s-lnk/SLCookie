{*
 * Cookie Popup for GDPR Cookie Consent.
 *
 * @author    Sergei
 * @copyright 2023 LINK Company
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *}

<!-- SLCookie -->
<div id="liveToast" class="opacity-25" style="padding: 1rem !important;
  right: 0 !important;
  bottom: 0 !important;
  position: fixed !important;" 
  role="alert" 
  aria-live="assertive" 
  aria-atomic="true">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-md">
            <div class="cart-content">
			   <h5>{$TITLE|escape:'htmlall':'UTF-8'}</h5>
               <p class="cart-products-count text-sm">{$TEXT|escape:'htmlall':'UTF-8'}<a href="{$URL|escape:'htmlall':'UTF-8'}">{$TEXT_URL|escape:'htmlall':'UTF-8'}</a></p>
            </div>
          </div>
        </div>	
      </div>
        <div class="modal-footer">           
              <div class="text-right">
                <!--<a href="" class="btn btn-primary text-sm" onclick="$('#liveToast').hide();"><i class="material-icons rtl-no-flip"></i>Nõus</a>-->
				<button type="button" class="btn btn-primary text-sm" onclick="setCookie('SLC','SLC',365);"><i class="material-icons rtl-no-flip"></i>{$BTN_CONFIRM|escape:'htmlall':'UTF-8'}</button>
              </div>
        </div>	      
    </div>
  </div>
<!-- SLCookie end -->

<script>
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function setCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";               

    document.cookie = name + "=" + value + expires + "; path=/";
	document.getElementById('liveToast').hidden = true;
}

var c = readCookie("SLC");

if (c == null ) {
	console.log("Acceptance cookie not set");
	document.getElementById('liveToast').hidden = false;
} else {
	console.log("Acceptance cookie is set");
	document.getElementById('liveToast').hidden = true;
}

</script>



