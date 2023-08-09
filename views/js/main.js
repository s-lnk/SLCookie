/**
 * Cookie Popup for GDPR Cookie Consent.
 *
 * @author    Sergei
 * @copyright 2023 LINK Company
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

(() => {
  const getCookie = (name) => {
    const value = " " + document.cookie;
    console.log("value", `==${value}==`);
    const parts = value.split(" " + name + "=");
    return parts.length < 2 ? undefined : parts.pop().split(";").shift();
  };

  const setCookie = function (name, value, expiryDays, domain, path, secure) {
    const exdate = new Date();
    exdate.setHours(
      exdate.getHours() +
        (typeof expiryDays !== "number" ? 365 : expiryDays) * 24
    );
    document.cookie =
      name +
      "=" +
      value +
      ";expires=" +
      exdate.toUTCString() +
      ";path=" +
      (path || "/") +
      (domain ? ";domain=" + domain : "") +
      (secure ? ";secure" : "");
  };

  const $cookiesBanner = document.querySelector(".cookies-eu-banner");
  const $cookiesBannerButton = $cookiesBanner.querySelector("button");
  const cookieName = "cookiesBanner";
  const hasCookie = getCookie(cookieName);

console.log('*** get cookie');

  if (!hasCookie) {
	  console.log('*** has cookie');
    $cookiesBanner.classList.remove("hidden");
  }

  $cookiesBannerButton.addEventListener("click", () => {
	  console.log('*** eventlisten');
    setCookie(cookieName, "closed");
    $cookiesBanner.remove();
  });
})();
