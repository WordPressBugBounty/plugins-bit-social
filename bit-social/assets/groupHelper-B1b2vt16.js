import{R as M,$ as K,a2 as H,w as L,A as pe,q as Z,v as Q,y as n,ab as ee,z as r,T as f,j as a,F as W,S as w,B as S,W as Le,D,E as te,r as O,I as U,L as j,M as ae,bL as re,a5 as E,U as x,s as ge,G as me,H as fe,x as Ce}from"./main-spicy-news-hang.js";import{p as Ae,P as ce,i as X}from"./PlatformIcon-zuCTLbxg.js";import{C as v,R as Y}from"./row-DPIcWscG.js";import{A as ye}from"./index-T4jDpJb8.js";import{F as B}from"./index-BANaAk1n.js";import{C as G}from"./index-CM_XzolY.js";import{A as xe}from"./index-61XRIzEU.js";import{s as z}from"./index-DMMaSiKI.js";import{P as Pe,F as I}from"./index-2k5fkCbF.js";import{I as V,S as Ie}from"./index-WU5SHOC8.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function ze({searchAccountsTxt:e="",selectedMenuTab:t={}}={}){const{isModalOpen:o}=M(K),c=[];e&&c.push(e),t.type&&c.push(t);const{data:s,isFetching:l,isLoading:u,refetch:i}=H({queryFn:async()=>L("accounts",void 0,{search:e,...t},"GET"),queryKey:["accounts",...c],refetchInterval:o?2e3:!1});return{accounts:(s==null?void 0:s.data)||[],isFetchingAccounts:l,isLoadingAccounts:u,refetchAccounts:i}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Ue(){const[e]=pe({}),t=e.get("value")||"",c={type:e.get("type")||"",value:t},s=Z(),{isError:l,isPending:u,isSuccess:i,mutateAsync:d}=Q({mutationFn:async p=>L("account/save",{accountData:p},void 0,"POST"),mutationKey:["save_account"],onSuccess:()=>{s.invalidateQueries({queryKey:["active-accounts"]}),s.invalidateQueries({queryKey:["accounts",c]})}});return{isSavingAccount:u,isSavingAccountError:l,isSavingAccountSuccess:i,saveAccount:d}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Ne(){const{isPending:e,mutateAsync:t}=Q({mutationFn:async o=>L(o.url,o.data,void 0,"POST")});return{isVerifyAccountLoading:e,verifyAccount:o=>t(o)}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const Re=e=>{switch(e){case"googleBusinessProfile":return n(W,{children:[r("You can only add verified locations."),a("br",{}),r("If you need to add another Google account, simply log out of or switch Google accounts first.")]});default:return a(W,{children:ee(r("If you need to add another %s  account, simply log out of or switch %s accounts first."),e,e)})}};function qe({count:e=0,platform:t}){return n(f.Text,{style:{fontSize:"14px"},type:"secondary",children:[n("div",{children:[ee(r("Total %s accounts found"),e)," "]}),Re(t)]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const Fe=({token:e})=>({background:`${e.colorBgLayout} `});function ne({accountType:e,connectAccountId:t,handleConnect:o,icon:c,id:s,isConnected:l,isLoading:u,name:i}){const d=JSON.parse(e).account_type;return a(G,{css:Fe,size:"small",children:n(Y,{align:"middle",justify:"space-between",children:[a(v,{children:n(w,{size:"small",children:[a(ye,{size:40,src:c}),n(B,{style:{maxWidth:"300px"},vertical:!0,children:[a(f.Text,{ellipsis:!0,strong:!0,title:i,children:i}),a(f.Text,{type:"secondary",children:d})]})]})}),a(v,{children:l?a(f.Text,{className:"mx-2",type:"success",children:r("Saved")}):a(S,{disabled:u&&s!==t,loading:u&&s===t,onClick:o,type:"primary",children:r("Save")})})]})})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function $e(){var R;const[e,t]=Le(K),{isProClient:o}=D(te),{isSavingAccount:c,saveAccount:s}=Ue(),{isVerifyAccountLoading:l,verifyAccount:u}=Ne(),[i,d]=O.useState(),p=()=>{t(re)},g=O.useMemo(()=>Array.isArray(e==null?void 0:e.details)?e.details.map(h=>{const{details:y}=h.account,{account_id:P,account_name:k,icon:_}=JSON.parse(y);return{accountDetails:h.account,accountIcon:_,accountId:P,accountName:k,isConnected:h.isConnected,verifyUrl:h.verifyUrl}}):[],[e==null?void 0:e.details]),m=async(h,y)=>{var _;d(h.account_id);let P={...h};if(y){const A=await u({data:P,url:y});if(A.status==="error")return d(void 0),z.error({description:A.data.message,message:"Account Save Failed"});P=A.data.accountDetails}const k=await s(P);if(d(void 0),k.status==="error")return z.error({description:k.data.message,message:"Account Save Failed"});if(k.status==="success"){const T=(_=e==null?void 0:e.details)==null?void 0:_.map(A=>{const J=A.account.account_id;return String(J)===String(k.data.account.account_id)?{...A,isConnected:!0}:A});t(A=>({...A,details:T})),z.success({message:"Account Save Successfully"})}},C=e.platform&&Ae[e.platform].name;return O.useEffect(()=>{function h(y){y.preventDefault(),t(re)}return window.addEventListener("beforeunload",h,{capture:!0}),()=>{window.removeEventListener("beforeunload",h,{capture:!0})}},[]),n(ae,{footer:!1,maskClosable:!1,onCancel:p,open:e.isModalOpen,styles:{body:{maxHeight:"70vh",overflowY:"scroll",padding:"0 8px"}},title:e.loading?n(w,{children:[a(U.Avatar,{active:!0,shape:"circle",size:"large"}),a(U.Input,{active:!0,size:"small"})]}):n(B,{align:"center",gap:"small",children:[a(ce,{name:e.platform,size:30}),a(f.Text,{style:{fontSize:"18px",textTransform:"capitalize"},children:C})]}),children:[n(w,{className:"w-100 py-2",direction:"vertical",children:[e.loading&&!e.status?Array.from({length:3}).fill(0).map((h,y)=>a(G,{className:"w-100",size:"small",children:n(w,{className:"w-100",size:"large",children:[a(U.Avatar,{active:!0,shape:"circle",size:"large"}),a(U.Input,{active:!0,block:!0,size:"small",style:{width:"260px"}}),a(U.Button,{active:!0})]})},`key-${y+9}`)):(C==="LinkedIn"&&!o?n(B,{gap:10,vertical:!0,children:[a(f.Title,{level:5,children:r("Profile")}),g&&g.length&&g.map(h=>a(ne,{accountType:h.accountDetails.details,connectAccountId:i,handleConnect:()=>m(h.accountDetails,h.verifyUrl),icon:h.accountIcon,id:h.accountId,isConnected:h.isConnected,isLoading:c||l,name:h.accountName},h.accountId)),n(f.Title,{level:5,children:[r("Page")," ",a(j,{color:"#ff8609",name:"crown",size:20})]}),a(G,{className:"w-100",size:"small",children:n(w,{className:"w-100",size:"large",children:[a(U.Avatar,{shape:"circle",size:"large"}),a(U.Input,{block:!0,size:"small",style:{width:"260px"}}),a(U.Button,{})]})}),a(f.Text,{strong:!0,children:r("Linkedin Page feature on pro")})]}):g&&g.length&&g.map(h=>a(ne,{accountType:h.accountDetails.details,connectAccountId:i,handleConnect:()=>m(h.accountDetails,h.verifyUrl),icon:h.accountIcon,id:h.accountId,isConnected:h.isConnected,isLoading:c||l,name:h.accountName},h.accountId)))||"",e.status==="success"&&!((R=e.details)!=null&&R.length)&&a(f.Title,{level:5,children:C==="Pinterest"?r("Board Not Found"):r("Account Not Found")}),e.status==="error"&&n(B,{vertical:!0,children:[n(f.Title,{level:5,children:[" ",r("Account connect failed")]}),a(xe,{description:e.message,type:"error"})]})]}),e.status&&C?a(qe,{count:g==null?void 0:g.length,platform:C}):a(f.Title,{level:5,children:"Fetching data, please wait..."})]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Oe(){const{data:e,isFetching:t,isLoading:o,refetch:c}=H({queryFn:async()=>L("platforms-credentials",void 0,void 0,"POST"),queryKey:["clientIds"]}),{clientInfo:s}=(e==null?void 0:e.data)||{};return{clientIds:s,isFetchingClientIds:t,isLoadingClientIds:o,refetchClientIds:c}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const{Link:Be}=f;function F({devLink:e,name:t}){return n(W,{children:[r("To get app credentials, Visit")," ",n(Be,{href:e,rel:"noopener noreferrer nofollow",strong:!0,style:{whiteSpace:"nowrap"},target:"_blank",children:[Ae[t].name," ",r("Developer"),a(j,{name:"move-up-right",size:12,style:{transform:"translateY(-2px)"}})]})]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const{Text:Ee}=f,Ve=e=>{switch(e){case"discord":return a(F,{devLink:"https://discord.com/developers/applications/",name:e});case"facebook":return a(F,{devLink:"https://developers.facebook.com/apps/",name:e});case"googleBusinessProfile":return a(F,{devLink:"https://console.cloud.google.com/",name:e});case"linkedin":return a(F,{devLink:"https://www.linkedin.com/developers/apps",name:e});case"pinterest":return a(F,{devLink:"https://developers.pinterest.com/apps/",name:e});case"tumblr":return a(F,{devLink:"https://www.tumblr.com/oauth/apps",name:e});case"twitter":return a(F,{devLink:"https://developer.twitter.com/en/portal/dashboard",name:e});default:return""}};function De({platform:e}){return a(Ee,{children:Ve(e)})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Ge(){const e=Z(),{isPending:t,mutateAsync:o}=Q({mutationFn:async c=>L("custom-app",{...c},void 0,"POST"),mutationKey:["custom_app"],onSuccess:()=>{e.invalidateQueries({queryKey:["get-customApp"]})}});return{createCustomApp:c=>o(c),isLoadingCreateCustomApp:t}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Ke(e){const{data:t,isLoading:o,refetch:c}=H({queryFn:async()=>L("custom-app",void 0,{platform:e},"GET"),queryKey:["get-customApp"],refetchOnWindowFocus:!1});return{customApps:t==null?void 0:t.data,isLoadingCustomApp:o,refetchCustomApp:c}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function He(){const e=Z(),{isPending:t,mutateAsync:o}=Q({mutationFn:async c=>L(`custom-app/${c}/destroy`,void 0,void 0,"POST"),onSuccess:()=>{e.invalidateQueries({queryKey:["get-customApp"]})}});return{deleteCustomApp:c=>o(c),isLoadingDeleteCustomApp:t}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const le={discord:"https://bit-social.com/docs/accounts/connect-your-discord-account-with-bit-social/",facebook:"https://bit-social.com/documentation/accounts/connect-your-facebook-account-with-bit-social/",googleBusinessProfile:"https://bit-social.com/documentation/accounts/connect-your-google-business-profile-account-with-bit-social/",instagram:"https://bit-social.com/documentation/accounts/connect-your-instagram-account-with-bit-social/",linkedin:"https://bit-social.com/documentation/accounts/connect-your-linkedin-account-with-bit-social/",pinterest:"https://bit-social.com/docs/accounts/connect-your-pinterest-account-with-bit-social/",tumblr:"https://bit-social.com/documentation/accounts/connect-your-tumblr-account-with-bit-social/",twitter:"https://bit-social.com/documentation/accounts/connect-your-twitter-account-with-bit-social/"};function Qe(e){if(!e||!le[e]){console.error("Invalid link key",e);return}const t=new URL(le[e]);return t.searchParams.append("utm_source",E.PLUGIN_SLUG),t.searchParams.append("utm_medium","inside-plugin"),t.href}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function be({iconName:e="file-text",platform:t}){return a(S,{href:Qe(t),icon:a(j,{name:e}),rel:"noopener noreferrer nofollow",size:"small",style:{whiteSpace:"nowrap"},target:"_blank",type:"link",children:r("Doc Link")})}const Ye="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20height='512'%20width='512'%20xmlns:v='https://vecta.io/nano'%3e%3cpath%20d='M501.301%20131.964c-5.888-22.172-23.237-39.633-45.266-45.56C416.107%2075.636%20256%2075.636%20256%2075.636s-160.107%200-200.035%2010.768c-22.029%205.926-39.378%2023.388-45.266%2045.56C0%20172.152%200%20256%200%20256s0%2083.848%2010.699%20124.036c5.888%2022.172%2023.237%2039.633%2045.266%2045.56C95.893%20436.364%20256%20436.364%20256%20436.364s160.107%200%20200.035-10.768c22.029-5.927%2039.378-23.388%2045.266-45.56C512%20339.848%20512%20256%20512%20256s0-83.848-10.699-124.036z'%20fill='red'/%3e%3cpath%20d='m203.636%20332.128%20133.818-76.126-133.818-76.13z'%20fill='%23fff'/%3e%3c/svg%3e";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const{Link:Je}=f,he={discord:"https://www.youtube.com/watch?v=q85NrjFQ33Y",facebook:"https://www.youtube.com/watch?v=V7-DqQ4dJg4",googleBusinessProfile:"https://www.youtube.com/watch?v=o3Hf2v4HsVs",linkedin:"https://www.youtube.com/watch?v=YcmVEq3GGsk",pinterest:"https://www.youtube.com/watch?v=6deQqqhMYLY",tumblr:"https://www.youtube.com/watch?v=WfxpNBBAjRQ",twitter:"https://www.youtube.com/watch?v=v2m99Itn-KU"};function we({platform:e}){if(!(!e||!he[e]))return a(Je,{href:he[e],rel:"noopener noreferrer nofollow",strong:!0,style:{whiteSpace:"nowrap"},target:"_blank",children:a(S,{icon:a(ye,{alt:"youtube",size:20,src:Ye}),size:"small",type:"text",children:r("Tutorial")})})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const Te=(e,t,o,c,s,l,u)=>{const[i,d]=[700,700],p=Math.round((window.innerWidth-i)/2),g=Math.round((window.innerHeight-d)/2),m=window.open(e,t,`width=${i},height=${d},left=${p},top=${g},toolbar=off`),C=setInterval(()=>{localStorage.setItem("__bitSocial_platform_interval",String(C)),!(!(m!=null&&m.closed)||m!=null&&m.closed&&!localStorage.getItem("__bitSocial_platform"))&&(clearInterval(C),o==null||o(),u(c,s,l))},500)};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const We=`${E.CLIENT_SECRET_URL}`,{baseAuthStateURL:Xe}=M(x),Ze=async e=>{const t={client_id:e.appId,platform:"facebook"},o={body:JSON.stringify(t),headers:{"Content-Type":"application/json"},method:"POST"};return fetch(We,o).then(c=>c.json())},et=async(e,t,o)=>{const c={...e};if(o==="oneClickAuth"){const i=await Ze(c);c.appSecret=i.clientSecret}const s={config:{authType:"OAuth2",platform:c.platform},payload:{client_id:c.appId,client_secret:c.appSecret,code:t.code,grant_type:"no need",redirect_uri:c.redirectUri}},{data:l,status:u}=await L("authorize",s,void 0,"POST");u==="success"&&ge(K,{details:l,isModalOpen:!0,loading:!1,platform:"facebook",status:u})},tt=async(e,t,o)=>{let c={};const s=localStorage.getItem("__bitSocial_platform");s&&(c=JSON.parse(s),localStorage.removeItem("__bitSocial_platform")),(!c||!c.code||c.error)&&z.error({description:`${c.error?`Cause: ${c.error}.`:""} Please try again`,message:"Authorization failed"});const l=await et(e,c,o);return t(),l},at=(e,t,o,c)=>{if(!e.platform||!e.appId||!e.redirectUri)throw new Error("Missing credentials");const{appId:s,platform:l,redirectUri:u}=e,i=c==="oneClickAuth"?E.REDIRECT_URL:u,d="pages_show_list,pages_read_engagement,pages_manage_metadata,pages_manage_posts,business_management",p=new URL("https://www.facebook.com/dialog/oauth");p.searchParams.append("scope",d),p.searchParams.append("client_id",s),p.searchParams.append("redirect_uri",i),p.searchParams.append("state",Xe+"#/accounts/auth/response"),Te(p,l,o,e,t,c,tt)};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const ct=`${E.CLIENT_SECRET_URL}`,ve=`${E.REDIRECT_URL}`,{baseAuthStateURL:ot}=M(x),st=async(e,t)=>{const o={config:{authType:"OAuth2",platform:e.platform},payload:{client_id:e.appId,client_secret:e.appSecret,code:t.code,grant_type:"null",redirect_uri:e.redirectUri}},{data:c,status:s}=await L("authorize",o,void 0,"POST");s==="success"&&ge(K,{...{isModalOpen:!0,loading:!1,platform:"linkedin",status:s},details:c})},it=async e=>{const t={client_id:e.appId,platform:e.platform},o={body:JSON.stringify(t),headers:{"Content-Type":"application/json"},method:"POST"};return fetch(ct,o).then(c=>c.json())},rt=async(e,t,o)=>{let c={};const s=localStorage.getItem("__bitSocial_platform"),l={...e};if(s&&(c=JSON.parse(s),localStorage.removeItem("__bitSocial_platform")),!c.code){z.error({description:`${c.error?`Cause: ${c.error}.`:""} Please try again`,message:"Authorization failed"});return}if(o==="oneClickAuth"){const i=await it(e);(!i||!i.clientSecret)&&z.error({description:`Cause: ${i.message}. Please try again`,message:"Authorization failed"}),l.appSecret=i.clientSecret.toString(),l.redirectUri=ve}const u=await st(l,c);return t(),u},nt=(e,t,o,c)=>{if(!e.platform||!e.appId||!e.redirectUri)throw new Error("Missing credentials");const{appId:s,platform:l,redirectUri:u}=e,i=c==="oneClickAuth"?ve:u,d="openid profile email w_member_social r_organization_admin w_organization_social rw_organization_admin",p=new URL("https://www.linkedin.com/oauth/v2/authorization");p.searchParams.append("scope",d),p.searchParams.append("client_id",s),p.searchParams.append("response_type","code"),p.searchParams.append("redirect_uri",i),p.searchParams.append("state",ot+"#/accounts/auth/response"),Te(p,l,o,e,t,c,rt)};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function lt(e){switch(e){case"facebook":return at;case"linkedin":return nt}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};M(x);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};M(x);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};M(x);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};M(x);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};M(x);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};M(x);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};M(x);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Se({authType:e="oneClickAuth",clientId:t,closeConnectModal:o,handleAuthCallback:c,isConnectLoading:s,platform:l,platformCredentialData:u,setCustomModalOpen:i}){const d=me(fe),p=lt;return a(S,{block:!0,disabled:!t,loading:s,onClick:()=>{const m=p(l,u.apiVersion);if(X(l)){d(!0);return}i&&i(!1),m&&m(u,c,o,e)},type:"primary",children:r("Connect")})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const{Text:ht}=f;var ut={name:"3s4yqf",styles:"width:400px"};function dt({closeConnectModal:e,credential:t,handleAuthCallback:o,handleDelete:c,isDeleteLoading:s,name:l,platform:u,setCustomModalOpen:i}){const d={apiVersion:t.apiVersion,appId:t.appKey,appSecret:t.appSecret,platform:u,redirectUri:t.redirectUri};return a(G,{css:ut,size:"small",children:n(Y,{align:"middle",justify:"space-between",children:[a(ht,{className:"text-capitalize",ellipsis:!0,strong:!0,style:{width:"200px"},children:l}),n(w,{children:[a(Se,{authType:"customAuth",clientId:t.appKey,closeConnectModal:e,handleAuthCallback:o,platform:u,platformCredentialData:d,setCustomModalOpen:i}),a(Pe,{cancelText:r("No"),description:r("Are you sure to delete this app?"),okButtonProps:{danger:!0,loading:s},okText:r("Yes, Delete"),onConfirm:c,placement:"bottomRight",title:r("Delete the app"),children:a(S,{danger:!0,icon:a(j,{name:"trash-2"}),type:"text"})})]})]})})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const{Text:$,Title:ue}=f,de=(e,t)=>({apiVersion:["twitter"],appName:["twitter","tumblr"]})[t].includes(e);var pt={name:"11vupei",styles:"border-right:1px solid #B9B9B9"};function gt({closeConnectModal:e,handleAuthCallback:t,onOk:o,open:c,platform:s,setAccountModal:l,setCustomModalOpen:u}){var oe,se,ie;const{apiURL:i,siteUrl:d}=D(x),p=`${i.base}/redirect`,g=d,[m]=I.useForm(),[C,R]=O.useState({}),{createCustomApp:h,isLoadingCreateCustomApp:y}=Ge(),{deleteCustomApp:P,isLoadingDeleteCustomApp:k}=He(),{customApps:_}=Ke(s),T=O.useRef(null),A=b=>{try{navigator.clipboard.writeText(b),z.success({message:"Copied!"})}catch{T.current!=null&&(T.current.select(),document.execCommand("copy"),T.current.blur())}},J=b=>{m.setFieldsValue({apiVersion:b})},ke=[{label:"v1.1 (media supported)",value:"1.1"},{label:"v2.0 (media unsupported)",value:"2"}],_e=async b=>{const Me={...b,platform:s},q=await h(Me);(q==null?void 0:q.code)==="SUCCESS"?(m.resetFields(),z.success({description:q.data.message,message:"App successfully added!"})):(q.data.errorField&&R(q.data.errorField),z.error({description:q.data.message,message:"App add failed"}))},je=b=>{P(b)};return a(ae,{centered:!0,footer:!1,onCancel:()=>u(!1),onOk:o,open:c,title:a("div",{children:n(B,{align:"center",gap:10,children:[a(S,{icon:a(j,{name:"arrow-left",size:24}),onClick:()=>{o(),l(!0)},title:r("Back"),type:"text"}),a(ce,{name:s,size:34}),a(ue,{className:"text-capitalize",level:5,style:{marginBottom:0},children:ee(r("Connect Custom %s App"),s)}),a(be,{platform:s}),a(we,{platform:s})]})}),width:900,children:n(Y,{className:"mt-4",gutter:[48,0],justify:"center",children:[a(v,{css:pt,span:12,children:n(I,{form:m,initialValues:{redirectUri:p},layout:"vertical",onFinish:_e,children:[a(I.Item,{label:a($,{strong:!0,children:r("Homepage URI")}),name:"homepageUri",rules:[{message:"Please enter homepage url"}],children:n(w.Compact,{className:"w-100",children:[a(V,{defaultValue:`${g}`,ref:T,style:{width:"calc(100% - 32px)"}}),a(S,{icon:a(j,{name:"copy",size:12}),onClick:()=>A(g),title:r("Click to copy")})]})}),a(I.Item,{label:a($,{strong:!0,children:r("Redirect URI")}),name:"redirectUri",rules:[{message:"Please enter redirect url",required:!0}],children:n(w.Compact,{className:"w-100",children:[a(V,{defaultValue:`${p}`,ref:T,style:{width:"calc(100% - 32px)"}}),a(S,{icon:a(j,{name:"copy",size:12}),onClick:()=>A(p),title:r("Click to copy")})]})}),de(s,"apiVersion")?a(I.Item,{label:a($,{strong:!0,children:r("Api Version")}),name:"apiVersion",rules:[{message:"Please select an API version",required:!0}],children:a(Ie,{onChange:J,options:ke})}):"",de(s,"appName")?n(I.Item,{label:a($,{strong:!0,children:r("App Name")}),name:"appName",rules:[{message:"Please enter your app name",required:!0}],children:[a(V,{}),C.appName&&a(f.Text,{type:"danger",children:(oe=C.appName)==null?void 0:oe[0]})]}):"",a(w,{className:"pb-2",children:a(De,{platform:s})}),n(I.Item,{label:a($,{strong:!0,children:r("App Key or Client ID")}),name:"appKey",rules:[{message:"Please enter your app key",required:!0}],children:[a(V,{}),C.appKey&&a(f.Text,{type:"danger",children:(se=C.appKey)==null?void 0:se[0]})]}),n(I.Item,{label:a($,{strong:!0,children:r("App Secret or Client Secret")}),name:"appSecret",rules:[{message:"Please enter your app secret",required:!0}],children:[a(V,{}),C.appSecret&&a(f.Text,{type:"danger",children:(ie=C.appSecret)==null?void 0:ie[0]})]}),a(I.Item,{children:a(S,{className:"w-100 pt-2",loading:y,onClick:()=>{m.submit()},size:"large",type:"primary",children:r("Add")})})]},s)}),n(v,{span:12,children:[a(ue,{level:5,children:r("Connected Apps")}),a(w,{direction:"vertical",children:_&&_.length?_.map(b=>a(dt,{appId:b.id,closeConnectModal:e,credential:b.credential,handleAuthCallback:t,handleDelete:()=>je(b.id),isDeleteLoading:k,name:b.name,platform:s,setCustomModalOpen:u},b.id)):a($,{children:r("No App Found")})})]})]})},s)}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const mt=`${E.REDIRECT_URL}`,{Title:ft}=f;function N({clientId:e,closeConnectModal:t,isConnectLoading:o,name:c,platform:s,setAccountModal:l,status:u}){const{refetchAccounts:i}=ze(),[d,p]=pe({}),g=d.get("value")||"all",[m,C]=O.useState(!1),{token:R}=Ce.useToken(),h=me(fe),{isProClient:y}=D(te),P={appId:e,appSecret:"",platform:s,redirectUri:mt},k=()=>{C(!1)},_=()=>{if(X(s)&&!y){h(!0);return}t(),C(!0)},T=()=>{p(A=>(A.set("type","platform"),A.set("value",String(g)),A)),i()};return a(G,{style:{borderColor:R.colorBorder,height:"100%"},children:n(B,{gap:14,vertical:!0,children:[n(w,{align:"center",children:[a(ce,{name:s,size:35,style:{flexShrink:0}}),a(ft,{className:"text-capitalize",level:5,style:{marginBottom:0},children:c||s}),X(s)&&!y&&a(j,{color:"#ff8609",name:"crown",size:20})]}),n(w,{children:[a(be,{platform:s}),a(we,{platform:s})]}),n(w,{direction:"vertical",size:"small",children:[a(S,{block:!0,onClick:_,style:{borderColor:R.colorPrimary},children:r("Custom App")}),u===0?void 0:a(Se,{clientId:e,closeConnectModal:t,handleAuthCallback:T,isConnectLoading:o,platform:s,platformCredentialData:P}),m&&s?a(gt,{closeConnectModal:t,handleAuthCallback:T,onOk:k,open:m,platform:s,setAccountModal:l,setCustomModalOpen:C}):void 0]})]})})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Mt({btnType:e="default"}){const{clientIds:t,isLoadingClientIds:o}=Oe(),{token:c}=Ce.useToken(),{isDarkTheme:s}=D(te),{isModalOpen:l}=D(K),[u,i]=O.useState(!1),d=()=>{const g=localStorage.getItem("__bitSocial_platform_interval");g&&(clearInterval(Number(g)),localStorage.removeItem("__bitSocial_platform_interval")),i(!1)},p=()=>{i(!0)};return n("div",{children:[e==="default"?a(S,{icon:a(j,{name:"plus",size:18}),onClick:p,size:"small",type:"primary",children:r("Connect Account")}):a(S,{"aria-label":r("Connect Account"),icon:a(j,{name:"plus",size:30}),onClick:p,style:{alignItems:"center",border:`2px solid ${c.colorPrimary}`,color:s?c.colorTextBase:c.colorPrimary,display:"flex",fontSize:"17px",fontWeight:600,height:"100%",justifyContent:"center",minHeight:"110px",width:"100%"},type:"text",children:r("Connect Account")}),a(ae,{centered:!0,footer:!1,onCancel:d,open:u,style:{padding:"20px"},title:r("Connect Account"),width:1e3,children:n(Y,{className:"p-2",gutter:[12,12],children:[a(v,{md:8,sm:12,xs:24,children:a(N,{clientId:t==null?void 0:t.facebook.id,closeConnectModal:d,isConnectLoading:o,platform:"facebook",setAccountModal:i,status:t==null?void 0:t.facebook.status})}),a(v,{md:8,sm:12,xs:24,children:a(N,{clientId:t==null?void 0:t.linkedin.id,closeConnectModal:d,isConnectLoading:o,platform:"linkedin",setAccountModal:i,status:t==null?void 0:t.linkedin.status})}),a(v,{md:8,sm:12,xs:24,children:a(N,{clientId:t==null?void 0:t.googleBusinessProfile.id,closeConnectModal:d,isConnectLoading:o,name:"Google Business",platform:"googleBusinessProfile",setAccountModal:i,status:t==null?void 0:t.googleBusinessProfile.status})}),a(v,{md:8,sm:12,xs:24,children:a(N,{clientId:t==null?void 0:t.pinterest.id,closeConnectModal:d,isConnectLoading:o,platform:"pinterest",setAccountModal:i,status:t==null?void 0:t.pinterest.status})}),a(v,{md:8,sm:12,xs:24,children:a(N,{clientId:t==null?void 0:t.discord.id,closeConnectModal:d,isConnectLoading:o,platform:"discord",setAccountModal:i,status:t==null?void 0:t.discord.status})}),a(v,{md:8,sm:12,xs:24,children:a(N,{clientId:t==null?void 0:t.tumblr.id,closeConnectModal:d,isConnectLoading:o,platform:"tumblr",setAccountModal:i,status:t==null?void 0:t.tumblr.status})}),a(v,{md:8,sm:12,xs:24,children:a(N,{clientId:t==null?void 0:t.instagram.id,closeConnectModal:d,isConnectLoading:o,platform:"instagram",setAccountModal:i,status:t==null?void 0:t.instagram.status})}),a(v,{md:8,sm:12,xs:24,children:a(N,{closeConnectModal:d,name:"X (Twitter)",platform:"twitter",setAccountModal:i,status:0})})]})}),l&&a($e,{})]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function Lt(){const{data:e,isLoading:t,refetch:o}=H({queryFn:async()=>L("pro_groups",void 0,void 0,"GET"),queryKey:["groups"]});return{groups:(e==null?void 0:e.data)||[],isLoadingGroups:t,refetchGroups:o}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const xt=e=>e.accounts.map(o=>Number(o.id)),Pt=(e,t,o)=>{const c=t.find(s=>s.name.toLowerCase()===e.toLowerCase());return!!c&&c.id!==o};export{Mt as C,Lt as a,xt as g,Pt as i,ze as u};
