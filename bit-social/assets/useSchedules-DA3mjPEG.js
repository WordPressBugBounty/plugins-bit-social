import{a2 as h,w as t}from"./main-honest-apples-shout.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,s){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,s),s)}};function u(){const{data:e,isLoading:s,refetch:c}=h({queryFn:async()=>t("schedule",void 0,void 0,"GET"),queryKey:["all-schedules"]});return{isLoadingSchedules:s,refetchSchedules:c,schedules:(e==null?void 0:e.data)||[]}}export{u};
