import{V as i,G as l,a2 as n,w as c}from"./main-spicy-news-hang.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const r={discord:{content:"{post_title}",postingType:"onlyMessage",trimMessage:!0},facebook:{content:"{post_title}",postingType:"onlyMessage",trimMessage:!0},googleBusinessProfile:{button:"none",content:"{post_title}",postingType:"onlyMessage",trimMessage:!0},instagram:{comment:"",content:"{post_title}",postingType:"isFeaturedImage",trimMessage:!0},linkedin:{content:"{post_title}",postingType:"onlyMessage",trimMessage:!0},pinterest:{content:"{post_title}",isLinkCard:!1,link:"",postingType:"isFeaturedImage",trimMessage:!0},tumblr:{content:"{post_title}",postingType:"onlyMessage",trimMessage:!0},twitter:{content:"{post_title}",postingType:"onlyMessage",trimMessage:!0}},a=globalThis.jotaiAtomCache.get("/home/runner/work/bit-social/bit-social/frontend/src/common/globalStates/socialTemplates/$socialTemplates.ts/$socialTemplates",i(r));a.debugLabel="$socialTemplates";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function g(){const e=l(a),{data:t,isLoading:o}=n({queryFn:async()=>{const{data:s}=await c("socialTemplates",void 0,void 0,"GET");return s?(e(s),s):[]},queryKey:["socialTemplates"]});return{isLoadingSocialTemplates:o,socialTemplates:t}}export{a as $,g as u};
