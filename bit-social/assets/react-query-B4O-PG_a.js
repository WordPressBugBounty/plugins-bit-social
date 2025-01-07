var fe=e=>{throw TypeError(e)};var $t=(e,t,s)=>t.has(e)||fe("Cannot "+s);var i=(e,t,s)=>($t(e,t,"read from private field"),s?s.call(e):t.get(e)),l=(e,t,s)=>t.has(e)?fe("Cannot add the same private member more than once"):t instanceof WeakSet?t.add(e):t.set(e,s),o=(e,t,s,r)=>($t(e,t,"write to private field"),r?r.call(e,s):t.set(e,s),s),p=(e,t,s)=>($t(e,t,"access private method"),s);var Kt=(e,t,s,r)=>({set _(n){o(e,t,n,s)},get _(){return i(e,t,r)}});import{r as D}from"./antd-Dkhz2BjJ.js";var Te={exports:{}},zt={};/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var Ve=D,Je=Symbol.for("react.element"),Ye=Symbol.for("react.fragment"),Xe=Object.prototype.hasOwnProperty,Ze=Ve.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,ts={key:!0,ref:!0,__self:!0,__source:!0};function Ae(e,t,s){var r,n={},a=null,u=null;s!==void 0&&(a=""+s),t.key!==void 0&&(a=""+t.key),t.ref!==void 0&&(u=t.ref);for(r in t)Xe.call(t,r)&&!ts.hasOwnProperty(r)&&(n[r]=t[r]);if(e&&e.defaultProps)for(r in t=e.defaultProps,t)n[r]===void 0&&(n[r]=t[r]);return{$$typeof:Je,type:e,key:a,ref:u,props:n,_owner:Ze.current}}zt.Fragment=Ye;zt.jsx=Ae;zt.jsxs=Ae;Te.exports=zt;var es=Te.exports,Tt=class{constructor(){this.listeners=new Set,this.subscribe=this.subscribe.bind(this)}subscribe(e){return this.listeners.add(e),this.onSubscribe(),()=>{this.listeners.delete(e),this.onUnsubscribe()}}hasListeners(){return this.listeners.size>0}onSubscribe(){}onUnsubscribe(){}},pt=typeof window>"u"||"Deno"in globalThis;function K(){}function ss(e,t){return typeof e=="function"?e(t):e}function Vt(e){return typeof e=="number"&&e>=0&&e!==1/0}function qe(e,t){return Math.max(e+(t||0)-Date.now(),0)}function gt(e,t){return typeof e=="function"?e(t):e}function G(e,t){return typeof e=="function"?e(t):e}function ye(e,t){const{type:s="all",exact:r,fetchStatus:n,predicate:a,queryKey:u,stale:h}=e;if(u){if(r){if(t.queryHash!==he(u,t.options))return!1}else if(!qt(t.queryKey,u))return!1}if(s!=="all"){const d=t.isActive();if(s==="active"&&!d||s==="inactive"&&d)return!1}return!(typeof h=="boolean"&&t.isStale()!==h||n&&n!==t.state.fetchStatus||a&&!a(t))}function pe(e,t){const{exact:s,status:r,predicate:n,mutationKey:a}=e;if(a){if(!t.options.mutationKey)return!1;if(s){if(mt(t.options.mutationKey)!==mt(a))return!1}else if(!qt(t.options.mutationKey,a))return!1}return!(r&&t.state.status!==r||n&&!n(t))}function he(e,t){return((t==null?void 0:t.queryKeyHashFn)||mt)(e)}function mt(e){return JSON.stringify(e,(t,s)=>Jt(s)?Object.keys(s).sort().reduce((r,n)=>(r[n]=s[n],r),{}):s)}function qt(e,t){return e===t?!0:typeof e!=typeof t?!1:e&&t&&typeof e=="object"&&typeof t=="object"?!Object.keys(t).some(s=>!qt(e[s],t[s])):!1}function Ue(e,t){if(e===t)return e;const s=me(e)&&me(t);if(s||Jt(e)&&Jt(t)){const r=s?e:Object.keys(e),n=r.length,a=s?t:Object.keys(t),u=a.length,h=s?[]:{};let d=0;for(let b=0;b<u;b++){const f=s?b:a[b];(!s&&r.includes(f)||s)&&e[f]===void 0&&t[f]===void 0?(h[f]=void 0,d++):(h[f]=Ue(e[f],t[f]),h[f]===e[f]&&e[f]!==void 0&&d++)}return n===u&&d===n?e:h}return t}function Gt(e,t){if(!t||Object.keys(e).length!==Object.keys(t).length)return!1;for(const s in e)if(e[s]!==t[s])return!1;return!0}function me(e){return Array.isArray(e)&&e.length===Object.keys(e).length}function Jt(e){if(!ve(e))return!1;const t=e.constructor;if(t===void 0)return!0;const s=t.prototype;return!(!ve(s)||!s.hasOwnProperty("isPrototypeOf")||Object.getPrototypeOf(e)!==Object.prototype)}function ve(e){return Object.prototype.toString.call(e)==="[object Object]"}function rs(e){return new Promise(t=>{setTimeout(t,e)})}function Yt(e,t,s){return typeof s.structuralSharing=="function"?s.structuralSharing(e,t):s.structuralSharing!==!1?Ue(e,t):t}function Ts(e){return e}function is(e,t,s=0){const r=[...e,t];return s&&r.length>s?r.slice(1):r}function ns(e,t,s=0){const r=[t,...e];return s&&r.length>s?r.slice(0,-1):r}var ce=Symbol();function je(e,t){return!e.queryFn&&(t!=null&&t.initialPromise)?()=>t.initialPromise:!e.queryFn||e.queryFn===ce?()=>Promise.reject(new Error(`Missing queryFn: '${e.queryHash}'`)):e.queryFn}var ot,Z,Ot,Re,as=(Re=class extends Tt{constructor(){super();l(this,ot);l(this,Z);l(this,Ot);o(this,Ot,t=>{if(!pt&&window.addEventListener){const s=()=>t();return window.addEventListener("visibilitychange",s,!1),()=>{window.removeEventListener("visibilitychange",s)}}})}onSubscribe(){i(this,Z)||this.setEventListener(i(this,Ot))}onUnsubscribe(){var t;this.hasListeners()||((t=i(this,Z))==null||t.call(this),o(this,Z,void 0))}setEventListener(t){var s;o(this,Ot,t),(s=i(this,Z))==null||s.call(this),o(this,Z,t(r=>{typeof r=="boolean"?this.setFocused(r):this.onFocus()}))}setFocused(t){i(this,ot)!==t&&(o(this,ot,t),this.onFocus())}onFocus(){const t=this.isFocused();this.listeners.forEach(s=>{s(t)})}isFocused(){var t;return typeof i(this,ot)=="boolean"?i(this,ot):((t=globalThis.document)==null?void 0:t.visibilityState)!=="hidden"}},ot=new WeakMap,Z=new WeakMap,Ot=new WeakMap,Re),le=new as,Ct,tt,wt,Se,us=(Se=class extends Tt{constructor(){super();l(this,Ct,!0);l(this,tt);l(this,wt);o(this,wt,t=>{if(!pt&&window.addEventListener){const s=()=>t(!0),r=()=>t(!1);return window.addEventListener("online",s,!1),window.addEventListener("offline",r,!1),()=>{window.removeEventListener("online",s),window.removeEventListener("offline",r)}}})}onSubscribe(){i(this,tt)||this.setEventListener(i(this,wt))}onUnsubscribe(){var t;this.hasListeners()||((t=i(this,tt))==null||t.call(this),o(this,tt,void 0))}setEventListener(t){var s;o(this,wt,t),(s=i(this,tt))==null||s.call(this),o(this,tt,t(this.setOnline.bind(this)))}setOnline(t){i(this,Ct)!==t&&(o(this,Ct,t),this.listeners.forEach(r=>{r(t)}))}isOnline(){return i(this,Ct)}},Ct=new WeakMap,tt=new WeakMap,wt=new WeakMap,Se),Bt=new us;function Xt(){let e,t;const s=new Promise((n,a)=>{e=n,t=a});s.status="pending",s.catch(()=>{});function r(n){Object.assign(s,n),delete s.resolve,delete s.reject}return s.resolve=n=>{r({status:"fulfilled",value:n}),e(n)},s.reject=n=>{r({status:"rejected",reason:n}),t(n)},s}function os(e){return Math.min(1e3*2**e,3e4)}function ke(e){return(e??"online")==="online"?Bt.isOnline():!0}var _e=class extends Error{constructor(e){super("CancelledError"),this.revert=e==null?void 0:e.revert,this.silent=e==null?void 0:e.silent}};function Wt(e){return e instanceof _e}function Le(e){let t=!1,s=0,r=!1,n;const a=Xt(),u=y=>{var v;r||(g(new _e(y)),(v=e.abort)==null||v.call(e))},h=()=>{t=!0},d=()=>{t=!1},b=()=>le.isFocused()&&(e.networkMode==="always"||Bt.isOnline())&&e.canRun(),f=()=>ke(e.networkMode)&&e.canRun(),c=y=>{var v;r||(r=!0,(v=e.onSuccess)==null||v.call(e,y),n==null||n(),a.resolve(y))},g=y=>{var v;r||(r=!0,(v=e.onError)==null||v.call(e,y),n==null||n(),a.reject(y))},C=()=>new Promise(y=>{var v;n=Q=>{(r||b())&&y(Q)},(v=e.onPause)==null||v.call(e)}).then(()=>{var y;n=void 0,r||(y=e.onContinue)==null||y.call(e)}),P=()=>{if(r)return;let y;const v=s===0?e.initialPromise:void 0;try{y=v??e.fn()}catch(Q){y=Promise.reject(Q)}Promise.resolve(y).then(c).catch(Q=>{var H;if(r)return;const I=e.retry??(pt?0:3),R=e.retryDelay??os,M=typeof R=="function"?R(s,Q):R,k=I===!0||typeof I=="number"&&s<I||typeof I=="function"&&I(s,Q);if(t||!k){g(Q);return}s++,(H=e.onFail)==null||H.call(e,s,Q),rs(M).then(()=>b()?void 0:C()).then(()=>{t?g(Q):P()})})};return{promise:a,cancel:u,continue:()=>(n==null||n(),a),cancelRetry:h,continueRetry:d,canStart:f,start:()=>(f()?P():C().then(P),a)}}function hs(){let e=[],t=0,s=h=>{h()},r=h=>{h()},n=h=>setTimeout(h,0);const a=h=>{t?e.push(h):n(()=>{s(h)})},u=()=>{const h=e;e=[],h.length&&n(()=>{r(()=>{h.forEach(d=>{s(d)})})})};return{batch:h=>{let d;t++;try{d=h()}finally{t--,t||u()}return d},batchCalls:h=>(...d)=>{a(()=>{h(...d)})},schedule:a,setNotifyFunction:h=>{s=h},setBatchNotifyFunction:h=>{r=h},setScheduler:h=>{n=h}}}var F=hs(),ht,Pe,Ke=(Pe=class{constructor(){l(this,ht)}destroy(){this.clearGcTimeout()}scheduleGc(){this.clearGcTimeout(),Vt(this.gcTime)&&o(this,ht,setTimeout(()=>{this.optionalRemove()},this.gcTime))}updateGcTime(e){this.gcTime=Math.max(this.gcTime||0,e??(pt?1/0:5*60*1e3))}clearGcTimeout(){i(this,ht)&&(clearTimeout(i(this,ht)),o(this,ht,void 0))}},ht=new WeakMap,Pe),Rt,St,L,x,Ut,ct,N,V,Ee,cs=(Ee=class extends Ke{constructor(t){super();l(this,N);l(this,Rt);l(this,St);l(this,L);l(this,x);l(this,Ut);l(this,ct);o(this,ct,!1),o(this,Ut,t.defaultOptions),this.setOptions(t.options),this.observers=[],o(this,L,t.cache),this.queryKey=t.queryKey,this.queryHash=t.queryHash,o(this,Rt,ls(this.options)),this.state=t.state??i(this,Rt),this.scheduleGc()}get meta(){return this.options.meta}get promise(){var t;return(t=i(this,x))==null?void 0:t.promise}setOptions(t){this.options={...i(this,Ut),...t},this.updateGcTime(this.options.gcTime)}optionalRemove(){!this.observers.length&&this.state.fetchStatus==="idle"&&i(this,L).remove(this)}setData(t,s){const r=Yt(this.state.data,t,this.options);return p(this,N,V).call(this,{data:r,type:"success",dataUpdatedAt:s==null?void 0:s.updatedAt,manual:s==null?void 0:s.manual}),r}setState(t,s){p(this,N,V).call(this,{type:"setState",state:t,setStateOptions:s})}cancel(t){var r,n;const s=(r=i(this,x))==null?void 0:r.promise;return(n=i(this,x))==null||n.cancel(t),s?s.then(K).catch(K):Promise.resolve()}destroy(){super.destroy(),this.cancel({silent:!0})}reset(){this.destroy(),this.setState(i(this,Rt))}isActive(){return this.observers.some(t=>G(t.options.enabled,this)!==!1)}isDisabled(){return this.getObserversCount()>0?!this.isActive():this.options.queryFn===ce||this.state.dataUpdateCount+this.state.errorUpdateCount===0}isStale(){return this.state.isInvalidated?!0:this.getObserversCount()>0?this.observers.some(t=>t.getCurrentResult().isStale):this.state.data===void 0}isStaleByTime(t=0){return this.state.isInvalidated||this.state.data===void 0||!qe(this.state.dataUpdatedAt,t)}onFocus(){var s;const t=this.observers.find(r=>r.shouldFetchOnWindowFocus());t==null||t.refetch({cancelRefetch:!1}),(s=i(this,x))==null||s.continue()}onOnline(){var s;const t=this.observers.find(r=>r.shouldFetchOnReconnect());t==null||t.refetch({cancelRefetch:!1}),(s=i(this,x))==null||s.continue()}addObserver(t){this.observers.includes(t)||(this.observers.push(t),this.clearGcTimeout(),i(this,L).notify({type:"observerAdded",query:this,observer:t}))}removeObserver(t){this.observers.includes(t)&&(this.observers=this.observers.filter(s=>s!==t),this.observers.length||(i(this,x)&&(i(this,ct)?i(this,x).cancel({revert:!0}):i(this,x).cancelRetry()),this.scheduleGc()),i(this,L).notify({type:"observerRemoved",query:this,observer:t}))}getObserversCount(){return this.observers.length}invalidate(){this.state.isInvalidated||p(this,N,V).call(this,{type:"invalidate"})}fetch(t,s){var d,b,f;if(this.state.fetchStatus!=="idle"){if(this.state.data!==void 0&&(s!=null&&s.cancelRefetch))this.cancel({silent:!0});else if(i(this,x))return i(this,x).continueRetry(),i(this,x).promise}if(t&&this.setOptions(t),!this.options.queryFn){const c=this.observers.find(g=>g.options.queryFn);c&&this.setOptions(c.options)}const r=new AbortController,n=c=>{Object.defineProperty(c,"signal",{enumerable:!0,get:()=>(o(this,ct,!0),r.signal)})},a=()=>{const c=je(this.options,s),g={queryKey:this.queryKey,meta:this.meta};return n(g),o(this,ct,!1),this.options.persister?this.options.persister(c,g,this):c(g)},u={fetchOptions:s,options:this.options,queryKey:this.queryKey,state:this.state,fetchFn:a};n(u),(d=this.options.behavior)==null||d.onFetch(u,this),o(this,St,this.state),(this.state.fetchStatus==="idle"||this.state.fetchMeta!==((b=u.fetchOptions)==null?void 0:b.meta))&&p(this,N,V).call(this,{type:"fetch",meta:(f=u.fetchOptions)==null?void 0:f.meta});const h=c=>{var g,C,P,y;Wt(c)&&c.silent||p(this,N,V).call(this,{type:"error",error:c}),Wt(c)||((C=(g=i(this,L).config).onError)==null||C.call(g,c,this),(y=(P=i(this,L).config).onSettled)==null||y.call(P,this.state.data,c,this)),this.scheduleGc()};return o(this,x,Le({initialPromise:s==null?void 0:s.initialPromise,fn:u.fetchFn,abort:r.abort.bind(r),onSuccess:c=>{var g,C,P,y;if(c===void 0){h(new Error(`${this.queryHash} data is undefined`));return}try{this.setData(c)}catch(v){h(v);return}(C=(g=i(this,L).config).onSuccess)==null||C.call(g,c,this),(y=(P=i(this,L).config).onSettled)==null||y.call(P,c,this.state.error,this),this.scheduleGc()},onError:h,onFail:(c,g)=>{p(this,N,V).call(this,{type:"failed",failureCount:c,error:g})},onPause:()=>{p(this,N,V).call(this,{type:"pause"})},onContinue:()=>{p(this,N,V).call(this,{type:"continue"})},retry:u.options.retry,retryDelay:u.options.retryDelay,networkMode:u.options.networkMode,canRun:()=>!0})),i(this,x).start()}},Rt=new WeakMap,St=new WeakMap,L=new WeakMap,x=new WeakMap,Ut=new WeakMap,ct=new WeakMap,N=new WeakSet,V=function(t){const s=r=>{switch(t.type){case"failed":return{...r,fetchFailureCount:t.failureCount,fetchFailureReason:t.error};case"pause":return{...r,fetchStatus:"paused"};case"continue":return{...r,fetchStatus:"fetching"};case"fetch":return{...r,...He(r.data,this.options),fetchMeta:t.meta??null};case"success":return{...r,data:t.data,dataUpdateCount:r.dataUpdateCount+1,dataUpdatedAt:t.dataUpdatedAt??Date.now(),error:null,isInvalidated:!1,status:"success",...!t.manual&&{fetchStatus:"idle",fetchFailureCount:0,fetchFailureReason:null}};case"error":const n=t.error;return Wt(n)&&n.revert&&i(this,St)?{...i(this,St),fetchStatus:"idle"}:{...r,error:n,errorUpdateCount:r.errorUpdateCount+1,errorUpdatedAt:Date.now(),fetchFailureCount:r.fetchFailureCount+1,fetchFailureReason:n,fetchStatus:"idle",status:"error"};case"invalidate":return{...r,isInvalidated:!0};case"setState":return{...r,...t.state}}};this.state=s(this.state),F.batch(()=>{this.observers.forEach(r=>{r.onQueryUpdate()}),i(this,L).notify({query:this,type:"updated",action:t})})},Ee);function He(e,t){return{fetchFailureCount:0,fetchFailureReason:null,fetchStatus:ke(t.networkMode)?"fetching":"paused",...e===void 0&&{error:null,status:"pending"}}}function ls(e){const t=typeof e.initialData=="function"?e.initialData():e.initialData,s=t!==void 0,r=s?typeof e.initialDataUpdatedAt=="function"?e.initialDataUpdatedAt():e.initialDataUpdatedAt:0;return{data:t,dataUpdateCount:0,dataUpdatedAt:s?r??Date.now():0,error:null,errorUpdateCount:0,errorUpdatedAt:0,fetchFailureCount:0,fetchFailureReason:null,fetchMeta:null,isInvalidated:!1,status:s?"success":"pending",fetchStatus:"idle"}}var B,Fe,ds=(Fe=class extends Tt{constructor(t={}){super();l(this,B);this.config=t,o(this,B,new Map)}build(t,s,r){const n=s.queryKey,a=s.queryHash??he(n,s);let u=this.get(a);return u||(u=new cs({cache:this,queryKey:n,queryHash:a,options:t.defaultQueryOptions(s),state:r,defaultOptions:t.getQueryDefaults(n)}),this.add(u)),u}add(t){i(this,B).has(t.queryHash)||(i(this,B).set(t.queryHash,t),this.notify({type:"added",query:t}))}remove(t){const s=i(this,B).get(t.queryHash);s&&(t.destroy(),s===t&&i(this,B).delete(t.queryHash),this.notify({type:"removed",query:t}))}clear(){F.batch(()=>{this.getAll().forEach(t=>{this.remove(t)})})}get(t){return i(this,B).get(t)}getAll(){return[...i(this,B).values()]}find(t){const s={exact:!0,...t};return this.getAll().find(r=>ye(s,r))}findAll(t={}){const s=this.getAll();return Object.keys(t).length>0?s.filter(r=>ye(t,r)):s}notify(t){F.batch(()=>{this.listeners.forEach(s=>{s(t)})})}onFocus(){F.batch(()=>{this.getAll().forEach(t=>{t.onFocus()})})}onOnline(){F.batch(()=>{this.getAll().forEach(t=>{t.onOnline()})})}},B=new WeakMap,Fe),z,A,lt,$,X,Qe,fs=(Qe=class extends Ke{constructor(t){super();l(this,$);l(this,z);l(this,A);l(this,lt);this.mutationId=t.mutationId,o(this,A,t.mutationCache),o(this,z,[]),this.state=t.state||Ne(),this.setOptions(t.options),this.scheduleGc()}setOptions(t){this.options=t,this.updateGcTime(this.options.gcTime)}get meta(){return this.options.meta}addObserver(t){i(this,z).includes(t)||(i(this,z).push(t),this.clearGcTimeout(),i(this,A).notify({type:"observerAdded",mutation:this,observer:t}))}removeObserver(t){o(this,z,i(this,z).filter(s=>s!==t)),this.scheduleGc(),i(this,A).notify({type:"observerRemoved",mutation:this,observer:t})}optionalRemove(){i(this,z).length||(this.state.status==="pending"?this.scheduleGc():i(this,A).remove(this))}continue(){var t;return((t=i(this,lt))==null?void 0:t.continue())??this.execute(this.state.variables)}async execute(t){var n,a,u,h,d,b,f,c,g,C,P,y,v,Q,I,R,M,k,H,T;o(this,lt,Le({fn:()=>this.options.mutationFn?this.options.mutationFn(t):Promise.reject(new Error("No mutationFn found")),onFail:(E,w)=>{p(this,$,X).call(this,{type:"failed",failureCount:E,error:w})},onPause:()=>{p(this,$,X).call(this,{type:"pause"})},onContinue:()=>{p(this,$,X).call(this,{type:"continue"})},retry:this.options.retry??0,retryDelay:this.options.retryDelay,networkMode:this.options.networkMode,canRun:()=>i(this,A).canRun(this)}));const s=this.state.status==="pending",r=!i(this,lt).canStart();try{if(!s){p(this,$,X).call(this,{type:"pending",variables:t,isPaused:r}),await((a=(n=i(this,A).config).onMutate)==null?void 0:a.call(n,t,this));const w=await((h=(u=this.options).onMutate)==null?void 0:h.call(u,t));w!==this.state.context&&p(this,$,X).call(this,{type:"pending",context:w,variables:t,isPaused:r})}const E=await i(this,lt).start();return await((b=(d=i(this,A).config).onSuccess)==null?void 0:b.call(d,E,t,this.state.context,this)),await((c=(f=this.options).onSuccess)==null?void 0:c.call(f,E,t,this.state.context)),await((C=(g=i(this,A).config).onSettled)==null?void 0:C.call(g,E,null,this.state.variables,this.state.context,this)),await((y=(P=this.options).onSettled)==null?void 0:y.call(P,E,null,t,this.state.context)),p(this,$,X).call(this,{type:"success",data:E}),E}catch(E){try{throw await((Q=(v=i(this,A).config).onError)==null?void 0:Q.call(v,E,t,this.state.context,this)),await((R=(I=this.options).onError)==null?void 0:R.call(I,E,t,this.state.context)),await((k=(M=i(this,A).config).onSettled)==null?void 0:k.call(M,void 0,E,this.state.variables,this.state.context,this)),await((T=(H=this.options).onSettled)==null?void 0:T.call(H,void 0,E,t,this.state.context)),E}finally{p(this,$,X).call(this,{type:"error",error:E})}}finally{i(this,A).runNext(this)}}},z=new WeakMap,A=new WeakMap,lt=new WeakMap,$=new WeakSet,X=function(t){const s=r=>{switch(t.type){case"failed":return{...r,failureCount:t.failureCount,failureReason:t.error};case"pause":return{...r,isPaused:!0};case"continue":return{...r,isPaused:!1};case"pending":return{...r,context:t.context,data:void 0,failureCount:0,failureReason:null,error:null,isPaused:t.isPaused,status:"pending",variables:t.variables,submittedAt:Date.now()};case"success":return{...r,data:t.data,failureCount:0,failureReason:null,error:null,status:"success",isPaused:!1};case"error":return{...r,data:void 0,error:t.error,failureCount:r.failureCount+1,failureReason:t.error,isPaused:!1,status:"error"}}};this.state=s(this.state),F.batch(()=>{i(this,z).forEach(r=>{r.onMutationUpdate(t)}),i(this,A).notify({mutation:this,type:"updated",action:t})})},Qe);function Ne(){return{context:void 0,data:void 0,error:null,failureCount:0,failureReason:null,isPaused:!1,status:"idle",variables:void 0,submittedAt:0}}var _,jt,De,ys=(De=class extends Tt{constructor(t={}){super();l(this,_);l(this,jt);this.config=t,o(this,_,new Map),o(this,jt,Date.now())}build(t,s,r){const n=new fs({mutationCache:this,mutationId:++Kt(this,jt)._,options:t.defaultMutationOptions(s),state:r});return this.add(n),n}add(t){const s=Ht(t),r=i(this,_).get(s)??[];r.push(t),i(this,_).set(s,r),this.notify({type:"added",mutation:t})}remove(t){var r;const s=Ht(t);if(i(this,_).has(s)){const n=(r=i(this,_).get(s))==null?void 0:r.filter(a=>a!==t);n&&(n.length===0?i(this,_).delete(s):i(this,_).set(s,n))}this.notify({type:"removed",mutation:t})}canRun(t){var r;const s=(r=i(this,_).get(Ht(t)))==null?void 0:r.find(n=>n.state.status==="pending");return!s||s===t}runNext(t){var r;const s=(r=i(this,_).get(Ht(t)))==null?void 0:r.find(n=>n!==t&&n.state.isPaused);return(s==null?void 0:s.continue())??Promise.resolve()}clear(){F.batch(()=>{this.getAll().forEach(t=>{this.remove(t)})})}getAll(){return[...i(this,_).values()].flat()}find(t){const s={exact:!0,...t};return this.getAll().find(r=>pe(s,r))}findAll(t={}){return this.getAll().filter(s=>pe(t,s))}notify(t){F.batch(()=>{this.listeners.forEach(s=>{s(t)})})}resumePausedMutations(){const t=this.getAll().filter(s=>s.state.isPaused);return F.batch(()=>Promise.all(t.map(s=>s.continue().catch(K))))}},_=new WeakMap,jt=new WeakMap,De);function Ht(e){var t;return((t=e.options.scope)==null?void 0:t.id)??String(e.mutationId)}function be(e){return{onFetch:(t,s)=>{var f,c,g,C,P;const r=t.options,n=(g=(c=(f=t.fetchOptions)==null?void 0:f.meta)==null?void 0:c.fetchMore)==null?void 0:g.direction,a=((C=t.state.data)==null?void 0:C.pages)||[],u=((P=t.state.data)==null?void 0:P.pageParams)||[];let h={pages:[],pageParams:[]},d=0;const b=async()=>{let y=!1;const v=R=>{Object.defineProperty(R,"signal",{enumerable:!0,get:()=>(t.signal.aborted?y=!0:t.signal.addEventListener("abort",()=>{y=!0}),t.signal)})},Q=je(t.options,t.fetchOptions),I=async(R,M,k)=>{if(y)return Promise.reject();if(M==null&&R.pages.length)return Promise.resolve(R);const H={queryKey:t.queryKey,pageParam:M,direction:k?"backward":"forward",meta:t.options.meta};v(H);const T=await Q(H),{maxPages:E}=t.options,w=k?ns:is;return{pages:w(R.pages,T,E),pageParams:w(R.pageParams,M,E)}};if(n&&a.length){const R=n==="backward",M=R?ps:ge,k={pages:a,pageParams:u},H=M(r,k);h=await I(k,H,R)}else{const R=e??a.length;do{const M=d===0?u[0]??r.initialPageParam:ge(r,h);if(d>0&&M==null)break;h=await I(h,M),d++}while(d<R)}return h};t.options.persister?t.fetchFn=()=>{var y,v;return(v=(y=t.options).persister)==null?void 0:v.call(y,b,{queryKey:t.queryKey,meta:t.options.meta,signal:t.signal},s)}:t.fetchFn=b}}}function ge(e,{pages:t,pageParams:s}){const r=t.length-1;return t.length>0?e.getNextPageParam(t[r],t,s[r],s):void 0}function ps(e,{pages:t,pageParams:s}){var r;return t.length>0?(r=e.getPreviousPageParam)==null?void 0:r.call(e,t[0],t,s[0],s):void 0}var S,et,st,Pt,Et,rt,Ft,Qt,Me,As=(Me=class{constructor(e={}){l(this,S);l(this,et);l(this,st);l(this,Pt);l(this,Et);l(this,rt);l(this,Ft);l(this,Qt);o(this,S,e.queryCache||new ds),o(this,et,e.mutationCache||new ys),o(this,st,e.defaultOptions||{}),o(this,Pt,new Map),o(this,Et,new Map),o(this,rt,0)}mount(){Kt(this,rt)._++,i(this,rt)===1&&(o(this,Ft,le.subscribe(async e=>{e&&(await this.resumePausedMutations(),i(this,S).onFocus())})),o(this,Qt,Bt.subscribe(async e=>{e&&(await this.resumePausedMutations(),i(this,S).onOnline())})))}unmount(){var e,t;Kt(this,rt)._--,i(this,rt)===0&&((e=i(this,Ft))==null||e.call(this),o(this,Ft,void 0),(t=i(this,Qt))==null||t.call(this),o(this,Qt,void 0))}isFetching(e){return i(this,S).findAll({...e,fetchStatus:"fetching"}).length}isMutating(e){return i(this,et).findAll({...e,status:"pending"}).length}getQueryData(e){var s;const t=this.defaultQueryOptions({queryKey:e});return(s=i(this,S).get(t.queryHash))==null?void 0:s.state.data}ensureQueryData(e){const t=this.defaultQueryOptions(e),s=i(this,S).build(this,t),r=s.state.data;return r===void 0?this.fetchQuery(e):(e.revalidateIfStale&&s.isStaleByTime(gt(t.staleTime,s))&&this.prefetchQuery(t),Promise.resolve(r))}getQueriesData(e){return i(this,S).findAll(e).map(({queryKey:t,state:s})=>{const r=s.data;return[t,r]})}setQueryData(e,t,s){const r=this.defaultQueryOptions({queryKey:e}),n=i(this,S).get(r.queryHash),a=n==null?void 0:n.state.data,u=ss(t,a);if(u!==void 0)return i(this,S).build(this,r).setData(u,{...s,manual:!0})}setQueriesData(e,t,s){return F.batch(()=>i(this,S).findAll(e).map(({queryKey:r})=>[r,this.setQueryData(r,t,s)]))}getQueryState(e){var s;const t=this.defaultQueryOptions({queryKey:e});return(s=i(this,S).get(t.queryHash))==null?void 0:s.state}removeQueries(e){const t=i(this,S);F.batch(()=>{t.findAll(e).forEach(s=>{t.remove(s)})})}resetQueries(e,t){const s=i(this,S),r={type:"active",...e};return F.batch(()=>(s.findAll(e).forEach(n=>{n.reset()}),this.refetchQueries(r,t)))}cancelQueries(e,t={}){const s={revert:!0,...t},r=F.batch(()=>i(this,S).findAll(e).map(n=>n.cancel(s)));return Promise.all(r).then(K).catch(K)}invalidateQueries(e,t={}){return F.batch(()=>{if(i(this,S).findAll(e).forEach(r=>{r.invalidate()}),(e==null?void 0:e.refetchType)==="none")return Promise.resolve();const s={...e,type:(e==null?void 0:e.refetchType)??(e==null?void 0:e.type)??"active"};return this.refetchQueries(s,t)})}refetchQueries(e,t={}){const s={...t,cancelRefetch:t.cancelRefetch??!0},r=F.batch(()=>i(this,S).findAll(e).filter(n=>!n.isDisabled()).map(n=>{let a=n.fetch(void 0,s);return s.throwOnError||(a=a.catch(K)),n.state.fetchStatus==="paused"?Promise.resolve():a}));return Promise.all(r).then(K)}fetchQuery(e){const t=this.defaultQueryOptions(e);t.retry===void 0&&(t.retry=!1);const s=i(this,S).build(this,t);return s.isStaleByTime(gt(t.staleTime,s))?s.fetch(t):Promise.resolve(s.state.data)}prefetchQuery(e){return this.fetchQuery(e).then(K).catch(K)}fetchInfiniteQuery(e){return e.behavior=be(e.pages),this.fetchQuery(e)}prefetchInfiniteQuery(e){return this.fetchInfiniteQuery(e).then(K).catch(K)}ensureInfiniteQueryData(e){return e.behavior=be(e.pages),this.ensureQueryData(e)}resumePausedMutations(){return Bt.isOnline()?i(this,et).resumePausedMutations():Promise.resolve()}getQueryCache(){return i(this,S)}getMutationCache(){return i(this,et)}getDefaultOptions(){return i(this,st)}setDefaultOptions(e){o(this,st,e)}setQueryDefaults(e,t){i(this,Pt).set(mt(e),{queryKey:e,defaultOptions:t})}getQueryDefaults(e){const t=[...i(this,Pt).values()],s={};return t.forEach(r=>{qt(e,r.queryKey)&&Object.assign(s,r.defaultOptions)}),s}setMutationDefaults(e,t){i(this,Et).set(mt(e),{mutationKey:e,defaultOptions:t})}getMutationDefaults(e){const t=[...i(this,Et).values()];let s={};return t.forEach(r=>{qt(e,r.mutationKey)&&(s={...s,...r.defaultOptions})}),s}defaultQueryOptions(e){if(e._defaulted)return e;const t={...i(this,st).queries,...this.getQueryDefaults(e.queryKey),...e,_defaulted:!0};return t.queryHash||(t.queryHash=he(t.queryKey,t)),t.refetchOnReconnect===void 0&&(t.refetchOnReconnect=t.networkMode!=="always"),t.throwOnError===void 0&&(t.throwOnError=!!t.suspense),!t.networkMode&&t.persister&&(t.networkMode="offlineFirst"),t.queryFn===ce&&(t.enabled=!1),t}defaultMutationOptions(e){return e!=null&&e._defaulted?e:{...i(this,st).mutations,...(e==null?void 0:e.mutationKey)&&this.getMutationDefaults(e.mutationKey),...e,_defaulted:!0}}clear(){i(this,S).clear(),i(this,et).clear()}},S=new WeakMap,et=new WeakMap,st=new WeakMap,Pt=new WeakMap,Et=new WeakMap,rt=new WeakMap,Ft=new WeakMap,Qt=new WeakMap,Me),U,m,kt,q,dt,Dt,it,W,_t,Mt,xt,ft,yt,nt,It,O,At,Zt,te,ee,se,re,ie,ne,Ge,xe,ms=(xe=class extends Tt{constructor(t,s){super();l(this,O);l(this,U);l(this,m);l(this,kt);l(this,q);l(this,dt);l(this,Dt);l(this,it);l(this,W);l(this,_t);l(this,Mt);l(this,xt);l(this,ft);l(this,yt);l(this,nt);l(this,It,new Set);this.options=s,o(this,U,t),o(this,W,null),o(this,it,Xt()),this.options.experimental_prefetchInRender||i(this,it).reject(new Error("experimental_prefetchInRender feature flag is not enabled")),this.bindMethods(),this.setOptions(s)}bindMethods(){this.refetch=this.refetch.bind(this)}onSubscribe(){this.listeners.size===1&&(i(this,m).addObserver(this),Oe(i(this,m),this.options)?p(this,O,At).call(this):this.updateResult(),p(this,O,se).call(this))}onUnsubscribe(){this.hasListeners()||this.destroy()}shouldFetchOnReconnect(){return ae(i(this,m),this.options,this.options.refetchOnReconnect)}shouldFetchOnWindowFocus(){return ae(i(this,m),this.options,this.options.refetchOnWindowFocus)}destroy(){this.listeners=new Set,p(this,O,re).call(this),p(this,O,ie).call(this),i(this,m).removeObserver(this)}setOptions(t,s){const r=this.options,n=i(this,m);if(this.options=i(this,U).defaultQueryOptions(t),this.options.enabled!==void 0&&typeof this.options.enabled!="boolean"&&typeof this.options.enabled!="function"&&typeof G(this.options.enabled,i(this,m))!="boolean")throw new Error("Expected enabled to be a boolean or a callback that returns a boolean");p(this,O,ne).call(this),i(this,m).setOptions(this.options),r._defaulted&&!Gt(this.options,r)&&i(this,U).getQueryCache().notify({type:"observerOptionsUpdated",query:i(this,m),observer:this});const a=this.hasListeners();a&&Ce(i(this,m),n,this.options,r)&&p(this,O,At).call(this),this.updateResult(s),a&&(i(this,m)!==n||G(this.options.enabled,i(this,m))!==G(r.enabled,i(this,m))||gt(this.options.staleTime,i(this,m))!==gt(r.staleTime,i(this,m)))&&p(this,O,Zt).call(this);const u=p(this,O,te).call(this);a&&(i(this,m)!==n||G(this.options.enabled,i(this,m))!==G(r.enabled,i(this,m))||u!==i(this,nt))&&p(this,O,ee).call(this,u)}getOptimisticResult(t){const s=i(this,U).getQueryCache().build(i(this,U),t),r=this.createResult(s,t);return bs(this,r)&&(o(this,q,r),o(this,Dt,this.options),o(this,dt,i(this,m).state)),r}getCurrentResult(){return i(this,q)}trackResult(t,s){const r={};return Object.keys(t).forEach(n=>{Object.defineProperty(r,n,{configurable:!1,enumerable:!0,get:()=>(this.trackProp(n),s==null||s(n),t[n])})}),r}trackProp(t){i(this,It).add(t)}getCurrentQuery(){return i(this,m)}refetch({...t}={}){return this.fetch({...t})}fetchOptimistic(t){const s=i(this,U).defaultQueryOptions(t),r=i(this,U).getQueryCache().build(i(this,U),s);return r.fetch().then(()=>this.createResult(r,s))}fetch(t){return p(this,O,At).call(this,{...t,cancelRefetch:t.cancelRefetch??!0}).then(()=>(this.updateResult(),i(this,q)))}createResult(t,s){var E;const r=i(this,m),n=this.options,a=i(this,q),u=i(this,dt),h=i(this,Dt),b=t!==r?t.state:i(this,kt),{state:f}=t;let c={...f},g=!1,C;if(s._optimisticResults){const w=this.hasListeners(),vt=!w&&Oe(t,s),bt=w&&Ce(t,r,s,n);(vt||bt)&&(c={...c,...He(f.data,t.options)}),s._optimisticResults==="isRestoring"&&(c.fetchStatus="idle")}let{error:P,errorUpdatedAt:y,status:v}=c;if(s.select&&c.data!==void 0)if(a&&c.data===(u==null?void 0:u.data)&&s.select===i(this,_t))C=i(this,Mt);else try{o(this,_t,s.select),C=s.select(c.data),C=Yt(a==null?void 0:a.data,C,s),o(this,Mt,C),o(this,W,null)}catch(w){o(this,W,w)}else C=c.data;if(s.placeholderData!==void 0&&C===void 0&&v==="pending"){let w;if(a!=null&&a.isPlaceholderData&&s.placeholderData===(h==null?void 0:h.placeholderData))w=a.data;else if(w=typeof s.placeholderData=="function"?s.placeholderData((E=i(this,xt))==null?void 0:E.state.data,i(this,xt)):s.placeholderData,s.select&&w!==void 0)try{w=s.select(w),o(this,W,null)}catch(vt){o(this,W,vt)}w!==void 0&&(v="success",C=Yt(a==null?void 0:a.data,w,s),g=!0)}i(this,W)&&(P=i(this,W),C=i(this,Mt),y=Date.now(),v="error");const Q=c.fetchStatus==="fetching",I=v==="pending",R=v==="error",M=I&&Q,k=C!==void 0,T={status:v,fetchStatus:c.fetchStatus,isPending:I,isSuccess:v==="success",isError:R,isInitialLoading:M,isLoading:M,data:C,dataUpdatedAt:c.dataUpdatedAt,error:P,errorUpdatedAt:y,failureCount:c.fetchFailureCount,failureReason:c.fetchFailureReason,errorUpdateCount:c.errorUpdateCount,isFetched:c.dataUpdateCount>0||c.errorUpdateCount>0,isFetchedAfterMount:c.dataUpdateCount>b.dataUpdateCount||c.errorUpdateCount>b.errorUpdateCount,isFetching:Q,isRefetching:Q&&!I,isLoadingError:R&&!k,isPaused:c.fetchStatus==="paused",isPlaceholderData:g,isRefetchError:R&&k,isStale:de(t,s),refetch:this.refetch,promise:i(this,it)};if(this.options.experimental_prefetchInRender){const w=Lt=>{T.status==="error"?Lt.reject(T.error):T.data!==void 0&&Lt.resolve(T.data)},vt=()=>{const Lt=o(this,it,T.promise=Xt());w(Lt)},bt=i(this,it);switch(bt.status){case"pending":t.queryHash===r.queryHash&&w(bt);break;case"fulfilled":(T.status==="error"||T.data!==bt.value)&&vt();break;case"rejected":(T.status!=="error"||T.error!==bt.reason)&&vt();break}}return T}updateResult(t){const s=i(this,q),r=this.createResult(i(this,m),this.options);if(o(this,dt,i(this,m).state),o(this,Dt,this.options),i(this,dt).data!==void 0&&o(this,xt,i(this,m)),Gt(r,s))return;o(this,q,r);const n={},a=()=>{if(!s)return!0;const{notifyOnChangeProps:u}=this.options,h=typeof u=="function"?u():u;if(h==="all"||!h&&!i(this,It).size)return!0;const d=new Set(h??i(this,It));return this.options.throwOnError&&d.add("error"),Object.keys(i(this,q)).some(b=>{const f=b;return i(this,q)[f]!==s[f]&&d.has(f)})};(t==null?void 0:t.listeners)!==!1&&a()&&(n.listeners=!0),p(this,O,Ge).call(this,{...n,...t})}onQueryUpdate(){this.updateResult(),this.hasListeners()&&p(this,O,se).call(this)}},U=new WeakMap,m=new WeakMap,kt=new WeakMap,q=new WeakMap,dt=new WeakMap,Dt=new WeakMap,it=new WeakMap,W=new WeakMap,_t=new WeakMap,Mt=new WeakMap,xt=new WeakMap,ft=new WeakMap,yt=new WeakMap,nt=new WeakMap,It=new WeakMap,O=new WeakSet,At=function(t){p(this,O,ne).call(this);let s=i(this,m).fetch(this.options,t);return t!=null&&t.throwOnError||(s=s.catch(K)),s},Zt=function(){p(this,O,re).call(this);const t=gt(this.options.staleTime,i(this,m));if(pt||i(this,q).isStale||!Vt(t))return;const r=qe(i(this,q).dataUpdatedAt,t)+1;o(this,ft,setTimeout(()=>{i(this,q).isStale||this.updateResult()},r))},te=function(){return(typeof this.options.refetchInterval=="function"?this.options.refetchInterval(i(this,m)):this.options.refetchInterval)??!1},ee=function(t){p(this,O,ie).call(this),o(this,nt,t),!(pt||G(this.options.enabled,i(this,m))===!1||!Vt(i(this,nt))||i(this,nt)===0)&&o(this,yt,setInterval(()=>{(this.options.refetchIntervalInBackground||le.isFocused())&&p(this,O,At).call(this)},i(this,nt)))},se=function(){p(this,O,Zt).call(this),p(this,O,ee).call(this,p(this,O,te).call(this))},re=function(){i(this,ft)&&(clearTimeout(i(this,ft)),o(this,ft,void 0))},ie=function(){i(this,yt)&&(clearInterval(i(this,yt)),o(this,yt,void 0))},ne=function(){const t=i(this,U).getQueryCache().build(i(this,U),this.options);if(t===i(this,m))return;const s=i(this,m);o(this,m,t),o(this,kt,t.state),this.hasListeners()&&(s==null||s.removeObserver(this),t.addObserver(this))},Ge=function(t){F.batch(()=>{t.listeners&&this.listeners.forEach(s=>{s(i(this,q))}),i(this,U).getQueryCache().notify({query:i(this,m),type:"observerResultsUpdated"})})},xe);function vs(e,t){return G(t.enabled,e)!==!1&&e.state.data===void 0&&!(e.state.status==="error"&&t.retryOnMount===!1)}function Oe(e,t){return vs(e,t)||e.state.data!==void 0&&ae(e,t,t.refetchOnMount)}function ae(e,t,s){if(G(t.enabled,e)!==!1){const r=typeof s=="function"?s(e):s;return r==="always"||r!==!1&&de(e,t)}return!1}function Ce(e,t,s,r){return(e!==t||G(r.enabled,e)===!1)&&(!s.suspense||e.state.status!=="error")&&de(e,s)}function de(e,t){return G(t.enabled,e)!==!1&&e.isStaleByTime(gt(t.staleTime,e))}function bs(e,t){return!Gt(e.getCurrentResult(),t)}var at,ut,j,J,Y,Nt,ue,Ie,gs=(Ie=class extends Tt{constructor(t,s){super();l(this,Y);l(this,at);l(this,ut);l(this,j);l(this,J);o(this,at,t),this.setOptions(s),this.bindMethods(),p(this,Y,Nt).call(this)}bindMethods(){this.mutate=this.mutate.bind(this),this.reset=this.reset.bind(this)}setOptions(t){var r;const s=this.options;this.options=i(this,at).defaultMutationOptions(t),Gt(this.options,s)||i(this,at).getMutationCache().notify({type:"observerOptionsUpdated",mutation:i(this,j),observer:this}),s!=null&&s.mutationKey&&this.options.mutationKey&&mt(s.mutationKey)!==mt(this.options.mutationKey)?this.reset():((r=i(this,j))==null?void 0:r.state.status)==="pending"&&i(this,j).setOptions(this.options)}onUnsubscribe(){var t;this.hasListeners()||(t=i(this,j))==null||t.removeObserver(this)}onMutationUpdate(t){p(this,Y,Nt).call(this),p(this,Y,ue).call(this,t)}getCurrentResult(){return i(this,ut)}reset(){var t;(t=i(this,j))==null||t.removeObserver(this),o(this,j,void 0),p(this,Y,Nt).call(this),p(this,Y,ue).call(this)}mutate(t,s){var r;return o(this,J,s),(r=i(this,j))==null||r.removeObserver(this),o(this,j,i(this,at).getMutationCache().build(i(this,at),this.options)),i(this,j).addObserver(this),i(this,j).execute(t)}},at=new WeakMap,ut=new WeakMap,j=new WeakMap,J=new WeakMap,Y=new WeakSet,Nt=function(){var s;const t=((s=i(this,j))==null?void 0:s.state)??Ne();o(this,ut,{...t,isPending:t.status==="pending",isSuccess:t.status==="success",isError:t.status==="error",isIdle:t.status==="idle",mutate:this.mutate,reset:this.reset})},ue=function(t){F.batch(()=>{var s,r,n,a,u,h,d,b;if(i(this,J)&&this.hasListeners()){const f=i(this,ut).variables,c=i(this,ut).context;(t==null?void 0:t.type)==="success"?((r=(s=i(this,J)).onSuccess)==null||r.call(s,t.data,f,c),(a=(n=i(this,J)).onSettled)==null||a.call(n,t.data,null,f,c)):(t==null?void 0:t.type)==="error"&&((h=(u=i(this,J)).onError)==null||h.call(u,t.error,f,c),(b=(d=i(this,J)).onSettled)==null||b.call(d,void 0,t.error,f,c))}this.listeners.forEach(f=>{f(i(this,ut))})})},Ie),Be=D.createContext(void 0),ze=e=>{const t=D.useContext(Be);if(!t)throw new Error("No QueryClient set, use QueryClientProvider to set one");return t},qs=({client:e,children:t})=>(D.useEffect(()=>(e.mount(),()=>{e.unmount()}),[e]),es.jsx(Be.Provider,{value:e,children:t})),$e=D.createContext(!1),Os=()=>D.useContext($e);$e.Provider;function Cs(){let e=!1;return{clearReset:()=>{e=!1},reset:()=>{e=!0},isReset:()=>e}}var ws=D.createContext(Cs()),Rs=()=>D.useContext(ws);function We(e,t){return typeof e=="function"?e(...t):!!e}function oe(){}var Ss=(e,t)=>{(e.suspense||e.throwOnError||e.experimental_prefetchInRender)&&(t.isReset()||(e.retryOnMount=!1))},Ps=e=>{D.useEffect(()=>{e.clearReset()},[e])},Es=({result:e,errorResetBoundary:t,throwOnError:s,query:r})=>e.isError&&!t.isReset()&&!e.isFetching&&r&&We(s,[e.error,r]),Fs=e=>{e.suspense&&(e.staleTime===void 0&&(e.staleTime=1e3),typeof e.gcTime=="number"&&(e.gcTime=Math.max(e.gcTime,1e3)))},Qs=(e,t)=>e.isLoading&&e.isFetching&&!t,Ds=(e,t)=>(e==null?void 0:e.suspense)&&t.isPending,we=(e,t,s)=>t.fetchOptimistic(e).catch(()=>{s.clearReset()});function Ms(e,t,s){var f,c,g,C,P;const r=ze(),n=Os(),a=Rs(),u=r.defaultQueryOptions(e);(c=(f=r.getDefaultOptions().queries)==null?void 0:f._experimental_beforeQuery)==null||c.call(f,u),u._optimisticResults=n?"isRestoring":"optimistic",Fs(u),Ss(u,a),Ps(a);const h=!r.getQueryCache().get(u.queryHash),[d]=D.useState(()=>new t(r,u)),b=d.getOptimisticResult(u);if(D.useSyncExternalStore(D.useCallback(y=>{const v=n?oe:d.subscribe(F.batchCalls(y));return d.updateResult(),v},[d,n]),()=>d.getCurrentResult(),()=>d.getCurrentResult()),D.useEffect(()=>{d.setOptions(u,{listeners:!1})},[u,d]),Ds(u,b))throw we(u,d,a);if(Es({result:b,errorResetBoundary:a,throwOnError:u.throwOnError,query:r.getQueryCache().get(u.queryHash)}))throw b.error;if((C=(g=r.getDefaultOptions().queries)==null?void 0:g._experimental_afterQuery)==null||C.call(g,u,b),u.experimental_prefetchInRender&&!pt&&Qs(b,n)){const y=h?we(u,d,a):(P=r.getQueryCache().get(u.queryHash))==null?void 0:P.promise;y==null||y.catch(oe).finally(()=>{d.updateResult()})}return u.notifyOnChangeProps?b:d.trackResult(b)}function Us(e,t){return Ms(e,ms)}function js(e,t){const s=ze(),[r]=D.useState(()=>new gs(s,e));D.useEffect(()=>{r.setOptions(e)},[r,e]);const n=D.useSyncExternalStore(D.useCallback(u=>r.subscribe(F.batchCalls(u)),[r]),()=>r.getCurrentResult(),()=>r.getCurrentResult()),a=D.useCallback((u,h)=>{r.mutate(u,h).catch(oe)},[r]);if(n.error&&We(r.options.throwOnError,[n.error]))throw n.error;return{...n,mutate:a,mutateAsync:n.mutate}}export{As as Q,ze as a,js as b,qs as c,es as j,Ts as k,Us as u};
