import{V as U,q as V,v as B,w as E,a2 as Z,a3 as ee,W as J,r as v,A as $,D as Q,E as te,z as a,y as g,j as s,B as A,L,S as j,M as ae,F as w,a4 as se,T as K,a5 as N,a6 as le}from"./main-honest-apples-shout.js";import{C as oe}from"./CronWarning-BJAezrvj.js";import{D as re,R as ce,F as ie}from"./Refresh-CY9WwX_L.js";import{u as ne}from"./useSchedules-DA3mjPEG.js";import{F as D,P as he}from"./index-CVSeideO.js";import{D as ue}from"./index-tZMVuNvr.js";import{S as z}from"./index-BbGC-JhL.js";import{T as x}from"./index-CJHP3YGL.js";import{s as de}from"./index-BhNz6GGj.js";import{F as G}from"./index-Dp2UqEVH.js";import{A as ge}from"./index-CDd3-czp.js";import"./addEventListener-CwYno9rX.js";import"./index-B20GHfvk.js";import"./index-O27KZoyf.js";import"./collapse-BbEVqHco.js";import"./index-CxuGqUmq.js";import"./index-kpwRjBig.js";import"./LeftOutlined-CVyNtSGf.js";import"./Dropdown-D4nmV-2U.js";import"./index-ClKoNv0W.js";import"./EllipsisOutlined-WRnUDywn.js";import"./move-BurAjF49.js";import"./index-9lCBX2l5.js";import"./row-C2vQMRT_.js";import"./EyeOutlined-CLK5SpBj.js";import"./context-CCE8EACh.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const H={date:"all",platform:"all",schedule:"all",scheduleId:"all",status:"all"},R=globalThis.jotaiAtomCache.get("/home/runner/work/bit-social/bit-social/frontend/src/common/globalStates/$logFilter.ts/$logFilter",U(H));R.debugLabel="$logFilter";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function me(e,t){const i=V(),{isPending:c,mutateAsync:o}=B({mutationFn:async r=>E("logs/destroy/batch",{logIds:r},void 0,"POST"),onSuccess:()=>{i.invalidateQueries({queryKey:["logs",[t,e]]})}});return{deleteLogs:r=>o(r),isLogsDeleting:c}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function pe(e,t,i){const{data:c,isFetched:o,isFetching:r,isLoading:n,refetch:h}=Z({enabled:!!t&&!!e,placeholderData:ee,queryFn:async()=>E(`logs/${e}/${t}`,void 0,{...i},"GET"),queryKey:["logs",[e,t]],refetchInterval:3e3}),{current_page:f,current_total:u,data:T,pages:b,per_page:C,total:P}=(c==null?void 0:c.data)||{};return{currentPage:f,currentTotal:u,isFetched:o,isFetching:r,isLogsLoading:n,logData:T||[],perPage:C,refetch:h,totalLogs:P,totalPages:b}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function fe(){const e=V(),{data:t,isPending:i,mutateAsync:c}=B({mutationFn:async o=>E("retry",{...o},void 0,"POST"),onSuccess:()=>{e.invalidateQueries({queryKey:["logs"]})}});return{isRetryLoading:i,result:t,retry:o=>c(o)}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};function be(){const[e,t]=J(R),[i,c]=v.useState(!1),[,o]=$({}),[r,n]=v.useState(),{schedules:h}=ne(),{isProClient:f}=Q(te),u=()=>{c(!0)},T=()=>{t(e),c(!1)},b=(d,F)=>{t(p=>({...p,[d]:F})),o(p=>(p.set("page","1"),p))},C=[{label:a("All"),value:"all"},{label:a("Facebook"),value:"facebook"},{label:a("Linkedin"),value:"linkedin"}],P=[{label:a("Google Business Profile"),value:"googleBusinessProfile"},{label:a("Pinterest"),value:"pinterest"},{label:a("Discord"),value:"discord"},{label:a("Tumblr"),value:"tumblr"},{label:a("X (twitter)"),value:"twitter"},{label:a("Instagram"),value:"instagram"}],M=f?[...C,...P]:C,k=h==null?void 0:h.map(d=>({label:d.name,value:String(d.id)})),y=[{label:a("All"),value:"all"},...k],_=d=>(F,p)=>{if(Array.isArray(p)&&p.every(O=>O===""))return b(d,"all");n(F),b(d,JSON.stringify(p))},I=()=>{n(void 0),t(H)};return g(w,{children:[s(j,{children:s(A,{icon:s(L,{name:"filter",size:12}),onClick:u,size:"large",children:a("Filter")})}),s(ae,{footer:[s(A,{onClick:I,type:"primary",children:a("Reset")},"reset")],onCancel:T,open:i,title:a("Logs filter"),width:600,children:g(D,{layout:"vertical",children:[s(D.Item,{label:a("Date"),children:s(ue.RangePicker,{className:"w-100",onChange:_("date"),value:r})}),s(D.Item,{label:a("Status"),children:s(z,{defaultValue:"all",onChange:d=>b("status",d),options:[{label:a("All"),value:"all"},{label:a("Success"),value:"1"},{label:a("Error"),value:"0"}],value:e.status})}),s(D.Item,{label:a("Schedule"),children:s(z,{defaultValue:"all",onChange:d=>b("schedule",d),options:y,value:e.schedule})}),s(D.Item,{label:a("Platform"),children:s(z,{defaultValue:"all",onChange:d=>b("platform",d),options:M,value:e.platform})})]})})]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const q={0:"error",1:"success"};function ye(){const[e,t]=J(R),[,i]=$({}),{platform:c,schedule:o,status:r}=e,n=e.date==="all"?!1:JSON.parse(e.date),h=f=>{t(u=>({...u,[f]:"all"})),i(u=>(u.set("page","1"),u))};return g(j,{size:"small",children:[a("Filter:"),n&&g(x,{closable:!0,color:"purple",onClose:()=>h("date"),children:[a("Date"),": ",n[0]," ",a("to")," ",n[1]]}),r!=="all"&&g(x,{closeIcon:!0,color:q[r],onClose:()=>h("status"),children:[a("Status"),": ",q[r]]}),o!=="all"&&g(x,{closable:!0,color:"orange",onClose:()=>h("schedule"),children:[a("Schedule"),": ",o," "]}),c!=="all"&&g(x,{closable:!0,color:"geekblue",onClose:()=>h("platform"),children:[a("Platform"),": ",c]})]})}const Ce="_expandDetails_vflxa_1",Se={expandDetails:Ce};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const Ae=(e,t,i)=>{e.stopPropagation(),i(t)},{Paragraph:Te,Text:ve}=K,xe=(e,t)=>[{dataIndex:"date",key:"date",title:a("Date"),width:"15%"},{dataIndex:"platform",key:"platform",title:a("Platform")},{dataIndex:"account",key:"account",title:a("Account")},{dataIndex:"schedule",key:"schedule",title:a("Schedule"),width:"20%"},{dataIndex:"details",key:"details",render:(c,{details:o,status:r})=>{const h=JSON.stringify(o);return g(G,{align:"center",children:[r==="1"?s(x,{color:"success",children:a("Success")}):s(x,{color:"error",children:a("Error")}),s(Te,{copyable:{text:h},style:{marginBottom:0},children:h.slice(0,20)})]})},title:a("Response"),width:"20%"},{dataIndex:"actions",key:"actions",render:(c,{details:o,key:r,scheduleId:n,status:h})=>s(w,{children:h==="1"?s(A,{href:o==null?void 0:o.post_url,icon:s(L,{name:"external-link",size:13}),size:"small",target:"_blank",children:a("Visit")}):s(A,{danger:!0,disabled:t||!n,icon:s(L,{name:"repeat",size:13}),onClick:f=>Ae(f,{details:o,key:r,scheduleId:n},e),size:"small",title:n?a("Retry"):a("Related schedule was deleted"),children:a("Retry")})}),title:a("Actions")}],Pe=e=>s(ge,{description:s("pre",{className:Se.expandDetails,children:JSON.stringify(e.details,void 0,2)}),message:e.status==="1"?"Success":"Error",showIcon:!0,type:e.status==="1"?"success":"error"}),ke=({expanded:e,onExpand:t,record:i})=>s(A,{onClick:c=>t(i,c),size:"small",children:e?a("Hide"):a("Details")}),Fe={expandedRowRender:Pe,expandIcon:ke,expandIconAsCell:!0,expandIconColumnIndex:6};function et(){const[e,t]=$({}),i=Number(e.get("page"))||1,c=Number(e.get("limit"))||10,[o,r]=v.useState([]),n=Q(R),{isFetching:h,isLogsLoading:f,logData:u,refetch:T,totalLogs:b,totalPages:C}=pe(i,c,n),{deleteLogs:P,isLogsDeleting:M}=me(c,i),{isRetryLoading:k,result:y,retry:_}=fe(),[I,d]=de.useMessage();v.useEffect(()=>{if(!k&&(y==null?void 0:y.status)==="success")return I.open({content:"Retry call successfully",type:"success"});if(!k&&(y==null?void 0:y.status)==="error")return I.open({content:"Retry call successfully",type:"success"})},[y]);const F=o.length>0,p=u&&(u==null?void 0:u.map(l=>{var S;return{account:l.details.account_name,date:se(l.created_at,`${N.DATE_FORMAT} ${N.TIME_FORMAT}`),details:l.details,key:l.id,platform:l.platform,schedule:((S=l==null?void 0:l.schedule)==null?void 0:S.name)||`Auto post - Post ID: ${l.details.post_id}`,scheduleId:l.schedule_id,status:l.status}})),O={onSelect:(l,S)=>{if(l){if(S){r(m=>[...m,l.key]);return}r(m=>m.filter(Y=>Y!==l.key))}},onSelectAll:(l,S)=>{if(l){r(S.map(m=>m==null?void 0:m.key).filter(m=>m!==void 0));return}r([])}},W=async()=>{await P(o),r([])},X=(l,S)=>{t(m=>(m.set("page",String(l)),m.set("limit",String(S)),m))};return v.useEffect(()=>{T(),r([])},[n]),v.useEffect(()=>{!u.length&&C&&i>C&&t(l=>(l.set("page",String(C)),l))},[u,n]),g(w,{children:[g(G,{className:"mb-2",justify:"space-between",children:[g(j,{children:[(f||M)&&s(le,{}),F?g(w,{children:[s(ve,{children:`Selected ${o.length} items`}),s(he,{description:a("Are you sure to delete this logs?"),okText:a("Confirm"),onConfirm:W,placement:"right",title:a("Delete Logs"),children:s(A,{danger:!0,icon:s(re,{}),type:"text"})})]}):void 0,Object.values(n).every(l=>l==="all")?void 0:s(ye,{})]}),g(j,{children:[g(K.Link,{href:"https://bit-social.com/docs/log/",rel:"noopener noreferrer",strong:!0,style:{fontSize:"18px"},target:"_blank",children:[a("Visit our documentation"),s(L,{name:"move-up-right",style:{transform:"translateY(-2px)"}})]}),s(A,{icon:s(ce,{spin:h}),loading:h,onClick:()=>T(),size:"large",children:a("Refresh")}),s(be,{})]})]}),s(oe,{}),s(ie,{bordered:!0,className:"mt-1",columns:xe(_,k),dataSource:p,expandable:Fe,pagination:{current:i,defaultCurrent:1,onChange:X,pageSize:c,showSizeChanger:!0,total:b},rowSelection:O}),d]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};export{et as default};
