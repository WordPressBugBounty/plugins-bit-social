import{bG as d}from"./main-spicy-news-hang.js";function r(){var e=document.documentElement.clientWidth,n=window.innerHeight||document.documentElement.clientHeight;return{width:e,height:n}}function f(e){var n=e.getBoundingClientRect(),t=document.documentElement;return{left:n.left+(window.pageXOffset||t.scrollLeft)-(t.clientLeft||document.body.clientLeft||0),top:n.top+(window.pageYOffset||t.scrollTop)-(t.clientTop||document.body.clientTop||0)}}function a(e,n,t,i){var o=d.unstable_batchedUpdates?function(l){d.unstable_batchedUpdates(t,l)}:t;return e!=null&&e.addEventListener&&e.addEventListener(n,o,i),{remove:function(){e!=null&&e.removeEventListener&&e.removeEventListener(n,o,i)}}}export{a,r as b,f as g};
