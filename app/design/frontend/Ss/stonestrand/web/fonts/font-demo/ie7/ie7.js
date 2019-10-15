/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'stone-strand\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-1': '&#xe900;',
		'icon-arrow-d': '&#xe901;',
		'icon-arrow-l': '&#xe902;',
		'icon-arrow-r': '&#xe903;',
		'icon-arrow-u': '&#xe904;',
		'icon-close': '&#xe905;',
		'icon-email': '&#xe906;',
		'icon-facebook': '&#xe907;',
		'icon-heart': '&#xe908;',
		'icon-instagram': '&#xe909;',
		'icon-lock': '&#xe90a;',
		'icon-messenger': '&#xe90b;',
		'icon-phone': '&#xe90c;',
		'icon-search': '&#xe90d;',
		'icon-twitter': '&#xe90e;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
