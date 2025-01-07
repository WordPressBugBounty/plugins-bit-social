import{r as $,u as W,b as X,a as h,j as a,L as j,_ as d,F as w,l as Y,m as B}from"./main-some-women-speak.js";import{C as U}from"./CronWarning-BDlyjNsw.js";import{R as Z,D as ee}from"./Refresh-vI4MAna2.js";import{a as J,b as Q,u as te,k as ae}from"./react-query-B4O-PG_a.js";import{r as C,B as S,S as R,o as L,u as se,v as N,M as le,q as v,w as oe,F as V,T as G,d as ne,P as ce,x as re,m as ie}from"./antd-Dkhz2BjJ.js";import{u as he}from"./router-BDBqwsZt.js";import{u as ue}from"./useSchedules-rRj8yXjM.js";import"./css-in-js-D9KZUreV.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function de(e,t){const n=J(),{isPending:l,mutateAsync:s}=Q({mutationFn:async c=>$("logs/destroy/batch",{logIds:c},void 0,"POST"),onSuccess:()=>{n.invalidateQueries({queryKey:["logs",[t,e]]})}});return{deleteLogs:c=>s(c),isLogsDeleting:l}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function ge(e,t,n){const{data:l,isFetched:s,isFetching:c,isLoading:r,refetch:g}=te({enabled:!!t&&!!e,placeholderData:ae,queryFn:async()=>$(`logs/${e}/${t}`,void 0,{...n},"GET"),queryKey:["logs",[e,t]],refetchInterval:3e3}),{current_page:f,current_total:T,data:m,pages:k,per_page:x,total:p}=(l==null?void 0:l.data)||{};return{currentPage:f,currentTotal:T,isFetched:s,isFetching:c,isLogsLoading:r,logData:m||[],perPage:x,refetch:g,totalLogs:p,totalPages:k}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function pe(){const e=J(),{data:t,isPending:n,mutateAsync:l}=Q({mutationFn:async s=>$("retry",{...s},void 0,"POST"),onSuccess:()=>{e.invalidateQueries({queryKey:["logs"]})}});return{isRetryLoading:n,result:t,retry:s=>l(s)}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const fe=(e,t)=>{const n=Object.keys(e),l=Object.keys(t);return n.length!==l.length?!1:n.every(s=>e[s]===t[s])};function me({filters:e,setFilters:t}){const[n,l]=C.useState(!1),[s,c]=C.useState(e),[r,g]=C.useState(),{schedules:f}=ue(),{isProClient:T}=W(X),m=()=>{l(!0)},k=()=>{t({...s}),l(!1)},x=()=>{c(e),l(!1)},p=(i,D)=>{c(A=>({...A,[i]:D}))},F=[{label:"All",value:"all"},{label:"Facebook",value:"facebook"},{label:"Linkedin",value:"linkedin"}],O=[{label:"Google Business Profile",value:"googleBusinessProfile"},{label:"Pinterest",value:"pinterest"},{label:"Discord",value:"discord"},{label:"Tumblr",value:"tumblr"},{label:"X (twitter)",value:"twitter"}],P=T?[...F,...O]:F,y=f==null?void 0:f.map(i=>({label:i.name,value:String(i.id)})),_=[{label:"All",value:"all"},...y],I=i=>(D,A)=>{if(Array.isArray(A)&&A.every(E=>E===""))return p(i,"all");g(D),p(i,JSON.stringify(A))},M=()=>{g(void 0),c({...q})},z=fe(q,s)?"Ok":"Filter";return h(w,{children:[a(R,{children:a(S,{icon:a(j,{name:"filter",size:12}),onClick:m,size:"large",children:d("Filter")})}),a(le,{footer:[a(S,{onClick:M,children:d("Reset")},"reset"),a(S,{onClick:k,type:"primary",children:z},"ok")],maskClosable:!1,onCancel:x,onOk:k,open:n,title:"Logs filter",width:600,children:h(L,{layout:"vertical",children:[a(L.Item,{label:"Date",children:a(se.RangePicker,{className:"w-100",onChange:I("date"),value:r})}),a(L.Item,{label:"Status",children:a(N,{defaultValue:"all",onChange:i=>p("status",i),options:[{label:"All",value:"all"},{label:"Success",value:"1"},{label:"Error",value:"2"}],value:s.status})}),a(L.Item,{label:"Schedule",children:a(N,{defaultValue:"all",onChange:i=>p("schedule",i),options:_,value:s.schedule})}),a(L.Item,{label:"Platform",children:a(N,{defaultValue:"all",onChange:i=>p("platform",i),options:P,value:s.platform})})]})})]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const K={1:"success",2:"error"};function ye({filters:e}){const{platform:t,schedule:n,status:l}=e,s=e.date==="all"?!1:JSON.parse(e.date);return h(R,{size:"small",children:[d("Filter:"),s&&h(v,{color:"purple",children:[d("Date"),": ",s[0]," to ",s[1]]}),l!=="all"&&h(v,{color:K[l],children:[d("Status"),": ",K[l]]}),n!=="all"&&h(v,{color:"orange",children:[d("Schedule"),": ",n," "]}),t!=="all"&&h(v,{color:"geekblue",children:[d("Platform"),": ",t]})]})}const be="_expandDetails_vflxa_1",Ce={expandDetails:be};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const Se=(e,t,n)=>{e.stopPropagation(),n(t)},{Paragraph:ke,Text:Ae}=G,ve=(e,t)=>[{dataIndex:"date",key:"date",title:"Date",width:"15%"},{dataIndex:"platform",key:"platform",title:"Platform"},{dataIndex:"account",key:"account",title:"Account"},{dataIndex:"schedule",key:"schedule",title:"Schedule",width:"20%"},{dataIndex:"details",key:"details",render:(l,{details:s,status:c})=>{const g=JSON.stringify(s);return h(V,{align:"center",children:[c==="1"?a(v,{color:"success",children:d("Success")}):a(v,{color:"error",children:d("Error")}),a(ke,{copyable:{text:g},ellipsis:!0,style:{marginBottom:0},children:g})]})},title:"Response",width:"20%"},{dataIndex:"actions",key:"actions",render:(l,{details:s,key:c,scheduleId:r,status:g})=>a(w,{children:g==="1"?a(S,{href:s==null?void 0:s.post_url,icon:a(j,{name:"external-link",size:13}),size:"small",target:"_blank",children:d("Visit")}):a(S,{danger:!0,disabled:t||!r,icon:a(j,{name:"repeat",size:13}),onClick:f=>Se(f,{details:s,key:c,scheduleId:r},e),size:"small",title:r?"Retry":"Related schedule was deleted",children:d("Retry")})}),title:"Actions"}],q={date:"all",platform:"all",schedule:"all",scheduleId:"all",status:"all"},Te=e=>a(ie,{description:a("pre",{className:Ce.expandDetails,children:JSON.stringify(e.details,void 0,2)}),message:e.status==="1"?"Success":"Error",showIcon:!0,type:e.status==="1"?"success":"error"}),xe=({expanded:e,onExpand:t,record:n})=>a(S,{onClick:l=>t(n,l),size:"small",children:e?"Hide":"Details"}),Pe={expandedRowRender:Te,expandIcon:xe,expandIconAsCell:!0,expandIconColumnIndex:6};function _e(){const[e,t]=he({}),n=Number(e.get("page"))||1,l=Number(e.get("limit"))||10,[s,c]=C.useState([]),[r,g]=C.useState(q),{isFetching:f,isLogsLoading:T,logData:m,refetch:k,totalLogs:x,totalPages:p}=ge(n,l,r),{deleteLogs:F,isLogsDeleting:O}=de(l,n),{isRetryLoading:P,result:y,retry:_}=pe(),[I,M]=oe.useMessage();C.useEffect(()=>{if(!P&&(y==null?void 0:y.status)==="success")return I.open({content:"Retry call successfully",type:"success"});if(!P&&(y==null?void 0:y.status)==="error")return I.open({content:"Retry call successfully",type:"success"})},[y]);const z=s.length>0,i=m&&(m==null?void 0:m.map(o=>{var b;return{account:o.details.account_name,date:Y(o.created_at,`${B.DATE_FORMAT} ${B.TIME_FORMAT}`),details:o.details,key:o.id,platform:o.platform,schedule:((b=o==null?void 0:o.schedule)==null?void 0:b.name)||`Auto post - Post ID: ${o.details.post_id}`,scheduleId:o.schedule_id,status:o.status}})),D={onSelect:(o,b)=>{if(o){if(b){c(u=>[...u,o.key]);return}c(u=>u.filter(H=>H!==o.key))}},onSelectAll:(o,b)=>{if(o){c(b.map(u=>u==null?void 0:u.key).filter(u=>u!==void 0));return}c([])}},A=async()=>{await F(s),c([])},E=(o,b)=>{t(u=>(u.set("page",String(o)),u.set("limit",String(b)),u))};return C.useEffect(()=>{k(),c([])},[r]),C.useEffect(()=>{!m.length&&p&&n>p&&t(o=>(o.set("page",String(p)),o))},[m,r]),h(w,{children:[h(V,{className:"mb-2",justify:"space-between",children:[h(R,{children:[(T||O)&&a(ne,{}),z?h(w,{children:[a(Ae,{children:`Selected ${s.length} items`}),a(ce,{description:"Are you sure to delete this logs?",okText:"Confirm",onConfirm:A,placement:"right",title:"Delete Logs",children:a(S,{danger:!0,icon:a(ee,{}),type:"text"})})]}):void 0,Object.values(r).every(o=>o==="all")?void 0:a(ye,{filters:r,setFilters:g})]}),h(R,{children:[h(G.Link,{href:"https://bit-social.com/docs/log/",rel:"noopener noreferrer",strong:!0,style:{fontSize:"18px"},target:"_blank",children:[d("Visit our documentation"),a(j,{name:"move-up-right",style:{transform:"translateY(-2px)"}})]}),a(S,{icon:a(Z,{spin:f}),loading:f,onClick:()=>k(),size:"large",children:d("Refresh")}),a(me,{filters:r,setFilters:g})]})]}),a(U,{}),a(re,{bordered:!0,className:"mt-1",columns:ve(_,P),dataSource:i,expandable:Pe,pagination:{current:n,defaultCurrent:1,onChange:E,pageSize:l,showSizeChanger:!0,total:x},rowSelection:D}),M]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};export{_e as default};
