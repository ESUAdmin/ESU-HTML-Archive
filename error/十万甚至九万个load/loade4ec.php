function isCompatible(str){var ua=str||navigator.userAgent;return!!((function(){'use strict';return!this&&Function.prototype.bind&&window.JSON;}())&&'querySelector'in document&&'localStorage'in window&&'addEventListener'in window&&!ua.match(/MSIE 10|webOS\/1\.[0-4]|SymbianOS|Series60|NetFront|Opera Mini|S40OviBrowser|MeeGo|Android.+Glass|^Mozilla\/5\.0 .+ Gecko\/$|googleweblight|PLAYSTATION|PlayStation/));}if(!isCompatible()){document.documentElement.className=document.documentElement.className.replace(/(^|\s)client-js(\s|$)/,'$1client-nojs$2');while(window.NORLQ&&window.NORLQ[0]){window.NORLQ.shift()();}window.NORLQ={push:function(fn){fn();}};window.RLQ={push:function(){}};}else{if(window.performance&&performance.mark){performance.mark('mwStartup');}(function(){'use strict';var mw,StringSet,log,hasOwn=Object.prototype.hasOwnProperty;function fnv132(str){var hash=0x811C9DC5,i=0;for(;i<str.length;i++){hash+=(hash<<1)+(hash<<4)+(hash<<7)+(hash<<8)+(hash<<24);hash^=str.charCodeAt(i);}hash
=(hash>>>0).toString(36);while(hash.length<7){hash='0'+hash;}return hash;}function defineFallbacks(){StringSet=window.Set||function(){var set=Object.create(null);return{add:function(value){set[value]=!0;},has:function(value){return value in set;}};};}function setGlobalMapValue(map,key,value){map.values[key]=value;log.deprecate(window,key,value,map===mw.config&&'Use mw.config instead.');}function logError(topic,data){var msg,e=data.exception,console=window.console;if(console&&console.log){msg=(e?'Exception':'Error')+' in '+data.source+(data.module?' in module '+data.module:'')+(e?':':'.');console.log(msg);if(e&&console.warn){console.warn(e);}}}function Map(global){this.values=Object.create(null);if(global===true){this.set=function(selection,value){var s;if(arguments.length>1){if(typeof selection==='string'){setGlobalMapValue(this,selection,value);return true;}}else if(typeof selection==='object'){for(s in selection){setGlobalMapValue(this,s,selection[s]);}return true;}return false;};}
}Map.prototype={constructor:Map,get:function(selection,fallback){var results,i;fallback=arguments.length>1?fallback:null;if(Array.isArray(selection)){results={};for(i=0;i<selection.length;i++){if(typeof selection[i]==='string'){results[selection[i]]=selection[i]in this.values?this.values[selection[i]]:fallback;}}return results;}if(typeof selection==='string'){return selection in this.values?this.values[selection]:fallback;}if(selection===undefined){results={};for(i in this.values){results[i]=this.values[i];}return results;}return fallback;},set:function(selection,value){var s;if(arguments.length>1){if(typeof selection==='string'){this.values[selection]=value;return true;}}else if(typeof selection==='object'){for(s in selection){this.values[s]=selection[s];}return true;}return false;},exists:function(selection){var i;if(Array.isArray(selection)){for(i=0;i<selection.length;i++){if(typeof selection[i]!=='string'||!(selection[i]in this.values)){return false;}}return true;}return typeof selection
==='string'&&selection in this.values;}};defineFallbacks();log=(function(){var log=function(){},console=window.console;log.warn=console&&console.warn?Function.prototype.bind.call(console.warn,console):function(){};log.error=console&&console.error?Function.prototype.bind.call(console.error,console):function(){};log.deprecate=function(obj,key,val,msg,logName){var stacks;function maybeLog(){var name=logName||key,trace=new Error().stack;if(!stacks){stacks=new StringSet();}if(!stacks.has(trace)){stacks.add(trace);if(logName||obj===window){mw.track('mw.deprecate',name);}mw.log.warn('Use of "'+name+'" is deprecated.'+(msg?' '+msg:''));}}try{Object.defineProperty(obj,key,{configurable:!0,enumerable:!0,get:function(){maybeLog();return val;},set:function(newVal){maybeLog();val=newVal;}});}catch(err){obj[key]=val;}};return log;}());mw={redefineFallbacksForTest:function(){if(!window.QUnit){throw new Error('Not allowed');}defineFallbacks();},now:function(){var perf=window.performance,navStart=
perf&&perf.timing&&perf.timing.navigationStart;mw.now=navStart&&perf.now?function(){return navStart+perf.now();}:Date.now;return mw.now();},trackQueue:[],track:function(topic,data){mw.trackQueue.push({topic:topic,timeStamp:mw.now(),data:data});},trackError:function(topic,data){mw.track(topic,data);logError(topic,data);},Map:Map,config:null,libs:{},legacy:{},messages:new Map(),templates:new Map(),log:log,loader:(function(){var registry=Object.create(null),sources=Object.create(null),handlingPendingRequests=!1,pendingRequests=[],queue=[],jobs=[],willPropagate=!1,errorModules=[],baseModules=["jquery","mediawiki.base"],marker=document.querySelector('meta[name="ResourceLoaderDynamicStyles"]'),nextCssBuffer,rAF=window.requestAnimationFrame||setTimeout;function newStyleTag(text,nextNode){var el=document.createElement('style');el.appendChild(document.createTextNode(text));if(nextNode&&nextNode.parentNode){nextNode.parentNode.insertBefore(el,nextNode);}else{document.head.appendChild(el);}
return el;}function flushCssBuffer(cssBuffer){var i;cssBuffer.active=!1;newStyleTag(cssBuffer.cssText,marker);for(i=0;i<cssBuffer.callbacks.length;i++){cssBuffer.callbacks[i]();}}function addEmbeddedCSS(cssText,callback){if(!nextCssBuffer||nextCssBuffer.active===false||cssText.slice(0,'@import'.length)==='@import'){nextCssBuffer={cssText:'',callbacks:[],active:null};}nextCssBuffer.cssText+='\n'+cssText;nextCssBuffer.callbacks.push(callback);if(nextCssBuffer.active===null){nextCssBuffer.active=!0;rAF(flushCssBuffer.bind(null,nextCssBuffer));}}function getCombinedVersion(modules){var hashes=modules.reduce(function(result,module){return result+registry[module].version;},'');return fnv132(hashes);}function allReady(modules){var i=0;for(;i<modules.length;i++){if(mw.loader.getState(modules[i])!=='ready'){return false;}}return true;}function allWithImplicitReady(module){return allReady(registry[module].dependencies)&&(baseModules.indexOf(module)!==-1||allReady(baseModules));}function
anyFailed(modules){var state,i=0;for(;i<modules.length;i++){state=mw.loader.getState(modules[i]);if(state==='error'||state==='missing'){return true;}}return false;}function doPropagation(){var errorModule,baseModuleError,module,i,failed,job,didPropagate=!0;do{didPropagate=!1;while(errorModules.length){errorModule=errorModules.shift();baseModuleError=baseModules.indexOf(errorModule)!==-1;for(module in registry){if(registry[module].state!=='error'&&registry[module].state!=='missing'){if(baseModuleError&&baseModules.indexOf(module)===-1){registry[module].state='error';didPropagate=!0;}else if(registry[module].dependencies.indexOf(errorModule)!==-1){registry[module].state='error';errorModules.push(module);didPropagate=!0;}}}}for(module in registry){if(registry[module].state==='loaded'&&allWithImplicitReady(module)){execute(module);didPropagate=!0;}}for(i=0;i<jobs.length;i++){job=jobs[i];failed=anyFailed(job.dependencies);if(failed||allReady(job.dependencies)){jobs.splice(i,1);i
-=1;try{if(failed&&job.error){job.error(new Error('Failed dependencies'),job.dependencies);}else if(!failed&&job.ready){job.ready();}}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'load-callback'});}didPropagate=!0;}}}while(didPropagate);willPropagate=!1;}function requestPropagation(){if(willPropagate){return;}willPropagate=!0;mw.requestIdleCallback(doPropagation,{timeout:1});}function setAndPropagate(module,state){registry[module].state=state;if(state==='loaded'||state==='ready'||state==='error'||state==='missing'){if(state==='ready'){mw.loader.store.add(module);}else if(state==='error'||state==='missing'){errorModules.push(module);}requestPropagation();}}function sortDependencies(module,resolved,unresolved){var i,skip,deps;if(!(module in registry)){throw new Error('Unknown module: '+module);}if(typeof registry[module].skip==='string'){skip=(new Function(registry[module].skip)());registry[module].skip=!!skip;if(skip){registry[module].dependencies=[];
setAndPropagate(module,'ready');return;}}if(!unresolved){unresolved=new StringSet();}deps=registry[module].dependencies;unresolved.add(module);for(i=0;i<deps.length;i++){if(resolved.indexOf(deps[i])===-1){if(unresolved.has(deps[i])){throw new Error('Circular reference detected: '+module+' -> '+deps[i]);}sortDependencies(deps[i],resolved,unresolved);}}resolved.push(module);}function resolve(modules){var resolved=baseModules.slice(),i=0;for(;i<modules.length;i++){sortDependencies(modules[i],resolved);}return resolved;}function resolveStubbornly(modules){var saved,resolved=baseModules.slice(),i=0;for(;i<modules.length;i++){saved=resolved.slice();try{sortDependencies(modules[i],resolved);}catch(err){resolved=saved;mw.trackError('resourceloader.exception',{exception:err,source:'resolve'});}}return resolved;}function resolveRelativePath(relativePath,basePath){var prefixes,prefix,baseDirParts,relParts=relativePath.match(/^((?:\.\.?\/)+)(.*)$/);if(!relParts){return null;}baseDirParts=basePath.
split('/');baseDirParts.pop();prefixes=relParts[1].split('/');prefixes.pop();while((prefix=prefixes.pop())!==undefined){if(prefix==='..'){baseDirParts.pop();}}return(baseDirParts.length?baseDirParts.join('/')+'/':'')+relParts[2];}function makeRequireFunction(moduleObj,basePath){return function require(moduleName){var fileName,fileContent,result,moduleParam,scriptFiles=moduleObj.script.files;fileName=resolveRelativePath(moduleName,basePath);if(fileName===null){return mw.loader.require(moduleName);}if(!hasOwn.call(scriptFiles,fileName)){throw new Error('Cannot require() undefined file '+fileName);}if(hasOwn.call(moduleObj.packageExports,fileName)){return moduleObj.packageExports[fileName];}fileContent=scriptFiles[fileName];if(typeof fileContent==='function'){moduleParam={exports:{}};fileContent(makeRequireFunction(moduleObj,fileName),moduleParam);result=moduleParam.exports;}else{result=fileContent;}moduleObj.packageExports[fileName]=result;return result;};}function addScript(src,callback
){var script=document.createElement('script');script.src=src;script.onload=script.onerror=function(){if(script.parentNode){script.parentNode.removeChild(script);}if(callback){callback();callback=null;}};document.head.appendChild(script);}function queueModuleScript(src,moduleName,callback){pendingRequests.push(function(){if(moduleName!=='jquery'){window.require=mw.loader.require;window.module=registry[moduleName].module;}addScript(src,function(){delete window.module;callback();if(pendingRequests[0]){pendingRequests.shift()();}else{handlingPendingRequests=!1;}});});if(!handlingPendingRequests&&pendingRequests[0]){handlingPendingRequests=!0;pendingRequests.shift()();}}function addLink(media,url){var el=document.createElement('link');el.rel='stylesheet';if(media&&media!=='all'){el.media=media;}el.href=url;if(marker&&marker.parentNode){marker.parentNode.insertBefore(el,marker);}else{document.head.appendChild(el);}}function domEval(code){var script=document.createElement('script');if(mw
.config.get('wgCSPNonce')!==false){script.nonce=mw.config.get('wgCSPNonce');}script.text=code;document.head.appendChild(script);script.parentNode.removeChild(script);}function enqueue(dependencies,ready,error){if(allReady(dependencies)){if(ready!==undefined){ready();}return;}if(anyFailed(dependencies)){if(error!==undefined){error(new Error('One or more dependencies failed to load'),dependencies);}return;}if(ready!==undefined||error!==undefined){jobs.push({dependencies:dependencies.filter(function(module){var state=registry[module].state;return state==='registered'||state==='loaded'||state==='loading'||state==='executing';}),ready:ready,error:error});}dependencies.forEach(function(module){if(registry[module].state==='registered'&&queue.indexOf(module)===-1){if(registry[module].group==='private'){setAndPropagate(module,'error');}else{queue.push(module);}}});mw.loader.work();}function execute(module){var key,value,media,i,urls,cssHandle,siteDeps,siteDepErr,runScript,cssPending=0;if(
registry[module].state!=='loaded'){throw new Error('Module in state "'+registry[module].state+'" may not be executed: '+module);}registry[module].state='executing';runScript=function(){var script,markModuleReady,nestedAddScript,mainScript;script=registry[module].script;markModuleReady=function(){setAndPropagate(module,'ready');};nestedAddScript=function(arr,callback,i){if(i>=arr.length){callback();return;}queueModuleScript(arr[i],module,function(){nestedAddScript(arr,callback,i+1);});};try{if(Array.isArray(script)){nestedAddScript(script,markModuleReady,0);}else if(typeof script==='function'||(typeof script==='object'&&script!==null)){if(typeof script==='function'){if(module==='jquery'){script();}else{script(window.$,window.$,mw.loader.require,registry[module].module);}}else{mainScript=script.files[script.main];if(typeof mainScript!=='function'){throw new Error('Main file '+script.main+' in module '+module+' must be of type function, found '+typeof mainScript);}mainScript(
makeRequireFunction(registry[module],script.main),registry[module].module);}markModuleReady();}else if(typeof script==='string'){domEval(script);markModuleReady();}else{markModuleReady();}}catch(e){setAndPropagate(module,'error');mw.trackError('resourceloader.exception',{exception:e,module:module,source:'module-execute'});}};if(registry[module].messages){mw.messages.set(registry[module].messages);}if(registry[module].templates){mw.templates.set(module,registry[module].templates);}cssHandle=function(){cssPending++;return function(){var runScriptCopy;cssPending--;if(cssPending===0){runScriptCopy=runScript;runScript=undefined;runScriptCopy();}};};if(registry[module].style){for(key in registry[module].style){value=registry[module].style[key];media=undefined;if(key!=='url'&&key!=='css'){if(typeof value==='string'){addEmbeddedCSS(value,cssHandle());}else{media=key;key='bc-url';}}if(Array.isArray(value)){for(i=0;i<value.length;i++){if(key==='bc-url'){addLink(media,value[i]);}else if(key===
'css'){addEmbeddedCSS(value[i],cssHandle());}}}else if(typeof value==='object'){for(media in value){urls=value[media];for(i=0;i<urls.length;i++){addLink(media,urls[i]);}}}}}if(module==='user'){try{siteDeps=resolve(['site']);}catch(e){siteDepErr=e;runScript();}if(siteDepErr===undefined){enqueue(siteDeps,runScript,runScript);}}else if(cssPending===0){runScript();}}function sortQuery(o){var key,sorted={},a=[];for(key in o){a.push(key);}a.sort();for(key=0;key<a.length;key++){sorted[a[key]]=o[a[key]];}return sorted;}function buildModulesString(moduleMap){var p,prefix,str=[],list=[];function restore(suffix){return p+suffix;}for(prefix in moduleMap){p=prefix===''?'':prefix+'.';str.push(p+moduleMap[prefix].join(','));list.push.apply(list,moduleMap[prefix].map(restore));}return{str:str.join('|'),list:list};}function resolveIndexedDependencies(modules){var i,j,deps;function resolveIndex(dep){return typeof dep==='number'?modules[dep][0]:dep;}for(i=0;i<modules.length;i++){deps=modules[i][2];if(
deps){for(j=0;j<deps.length;j++){deps[j]=resolveIndex(deps[j]);}}}}function makeQueryString(params){return Object.keys(params).map(function(key){return encodeURIComponent(key)+'='+encodeURIComponent(params[key]);}).join('&');}function batchRequest(batch){var reqBase,splits,b,bSource,bGroup,source,group,i,modules,sourceLoadScript,currReqBase,currReqBaseLength,moduleMap,currReqModules,l,lastDotIndex,prefix,suffix,bytesAdded;function doRequest(){var query=Object.create(currReqBase),packed=buildModulesString(moduleMap);query.modules=packed.str;query.version=getCombinedVersion(packed.list);query=sortQuery(query);addScript(sourceLoadScript+'?'+makeQueryString(query));}if(!batch.length){return;}batch.sort();reqBase={skin:mw.config.get('skin'),lang:mw.config.get('wgUserLanguage'),debug:mw.config.get('debug')};splits=Object.create(null);for(b=0;b<batch.length;b++){bSource=registry[batch[b]].source;bGroup=registry[batch[b]].group;if(!splits[bSource]){splits[bSource]=Object.create(null);}if(!
splits[bSource][bGroup]){splits[bSource][bGroup]=[];}splits[bSource][bGroup].push(batch[b]);}for(source in splits){sourceLoadScript=sources[source];for(group in splits[source]){modules=splits[source][group];currReqBase=Object.create(reqBase);if(group==='user'&&mw.config.get('wgUserName')!==null){currReqBase.user=mw.config.get('wgUserName');}currReqBaseLength=makeQueryString(currReqBase).length+25;l=currReqBaseLength;moduleMap=Object.create(null);currReqModules=[];for(i=0;i<modules.length;i++){lastDotIndex=modules[i].lastIndexOf('.');prefix=modules[i].substr(0,lastDotIndex);suffix=modules[i].slice(lastDotIndex+1);bytesAdded=moduleMap[prefix]?suffix.length+3:modules[i].length+3;if(currReqModules.length&&l+bytesAdded>mw.loader.maxQueryLength){doRequest();l=currReqBaseLength;moduleMap=Object.create(null);currReqModules=[];mw.track('resourceloader.splitRequest',{maxQueryLength:mw.loader.maxQueryLength});}if(!moduleMap[prefix]){moduleMap[prefix]=[];}l+=bytesAdded;moduleMap[prefix].push(
suffix);currReqModules.push(modules[i]);}if(currReqModules.length){doRequest();}}}}function asyncEval(implementations,cb){if(!implementations.length){return;}mw.requestIdleCallback(function(){try{domEval(implementations.join(';'));}catch(err){cb(err);}});}function getModuleKey(module){return module in registry?(module+'@'+registry[module].version):null;}function splitModuleKey(key){var index=key.indexOf('@');if(index===-1){return{name:key,version:''};}return{name:key.slice(0,index),version:key.slice(index+1)};}function registerOne(module,version,dependencies,group,source,skip){if(module in registry){throw new Error('module already registered: '+module);}registry[module]={module:{exports:{}},packageExports:{},version:String(version||''),dependencies:dependencies||[],group:typeof group==='string'?group:null,source:typeof source==='string'?source:'local',state:'registered',skip:typeof skip==='string'?skip:null};}return{moduleRegistry:registry,maxQueryLength:2000,addStyleTag:newStyleTag,
enqueue:enqueue,resolve:resolve,work:function(){var implementations,sourceModules,batch=[],q=0;for(;q<queue.length;q++){if(queue[q]in registry&&registry[queue[q]].state==='registered'){if(batch.indexOf(queue[q])===-1){batch.push(queue[q]);registry[queue[q]].state='loading';}}}queue=[];if(!batch.length){return;}mw.loader.store.init();if(mw.loader.store.enabled){implementations=[];sourceModules=[];batch=batch.filter(function(module){var implementation=mw.loader.store.get(module);if(implementation){implementations.push(implementation);sourceModules.push(module);return false;}return true;});asyncEval(implementations,function(err){var failed;mw.loader.store.stats.failed++;mw.loader.store.clear();mw.trackError('resourceloader.exception',{exception:err,source:'store-eval'});failed=sourceModules.filter(function(module){return registry[module].state==='loading';});batchRequest(failed);});}batchRequest(batch);},addSource:function(ids){var id;for(id in ids){if(id in sources){throw new Error(
'source already registered: '+id);}sources[id]=ids[id];}},register:function(modules){var i;if(typeof modules==='object'){resolveIndexedDependencies(modules);for(i=0;i<modules.length;i++){registerOne.apply(null,modules[i]);}}else{registerOne.apply(null,arguments);}},implement:function(module,script,style,messages,templates){var split=splitModuleKey(module),name=split.name,version=split.version;if(!(name in registry)){mw.loader.register(name);}if(registry[name].script!==undefined){throw new Error('module already implemented: '+name);}if(version){registry[name].version=version;}registry[name].script=script||null;registry[name].style=style||null;registry[name].messages=messages||null;registry[name].templates=templates||null;if(registry[name].state!=='error'&&registry[name].state!=='missing'){setAndPropagate(name,'loaded');}},load:function(modules,type){var filtered,l;if(typeof modules==='string'){if(/^(https?:)?\/?\//.test(modules)){if(type==='text/css'){l=document.createElement('link');l.
rel='stylesheet';l.href=modules;document.head.appendChild(l);return;}if(type==='text/javascript'||type===undefined){addScript(modules);return;}throw new Error('type must be text/css or text/javascript, found '+type);}modules=[modules];}filtered=modules.filter(function(module){var state=mw.loader.getState(module);return state!=='error'&&state!=='missing';});filtered=resolveStubbornly(filtered);enqueue(filtered,undefined,undefined);},state:function(states){var module,state;for(module in states){state=states[module];if(!(module in registry)){mw.loader.register(module);}setAndPropagate(module,state);}},getVersion:function(module){return module in registry?registry[module].version:null;},getState:function(module){return module in registry?registry[module].state:null;},getModuleNames:function(){return Object.keys(registry);},require:function(moduleName){var state=mw.loader.getState(moduleName);if(state!=='ready'){throw new Error('Module "'+moduleName+'" is not loaded');}return registry[
moduleName].module.exports;},store:{enabled:null,MODULE_SIZE_MAX:100*1000,items:{},queue:[],stats:{hits:0,misses:0,expired:0,failed:0},toJSON:function(){return{items:mw.loader.store.items,vary:mw.loader.store.getVary()};},getStoreKey:function(){return'MediaWikiModuleStore:'+mw.config.get('wgDBname');},getVary:function(){return mw.config.get('skin')+':'+mw.config.get('wgResourceLoaderStorageVersion')+':'+mw.config.get('wgUserLanguage');},init:function(){var raw,data;if(this.enabled!==null){return;}if(/Firefox/.test(navigator.userAgent)||!mw.config.get('wgResourceLoaderStorageEnabled')){this.clear();this.enabled=!1;return;}if(mw.config.get('debug')){this.enabled=!1;return;}try{raw=localStorage.getItem(this.getStoreKey());this.enabled=!0;data=JSON.parse(raw);if(data&&typeof data.items==='object'&&data.vary===this.getVary()){this.items=data.items;return;}}catch(e){}if(raw===undefined){this.enabled=!1;}},get:function(module){var key;if(!this.enabled){return false;}key=
getModuleKey(module);if(key in this.items){this.stats.hits++;return this.items[key];}this.stats.misses++;return false;},add:function(module){if(!this.enabled){return;}this.queue.push(module);this.requestUpdate();},set:function(module){var key,args,src,encodedScript,descriptor=mw.loader.moduleRegistry[module];key=getModuleKey(module);if(key in this.items||!descriptor||descriptor.state!=='ready'||!descriptor.version||descriptor.group==='private'||descriptor.group==='user'||[descriptor.script,descriptor.style,descriptor.messages,descriptor.templates].indexOf(undefined)!==-1){return;}try{if(typeof descriptor.script==='function'){encodedScript=String(descriptor.script);}else if(typeof descriptor.script==='object'&&descriptor.script&&!Array.isArray(descriptor.script)){encodedScript='{'+'main:'+JSON.stringify(descriptor.script.main)+','+'files:{'+Object.keys(descriptor.script.files).map(function(key){var value=descriptor.script.files[key];return JSON.stringify(key)+':'+(typeof value===
'function'?value:JSON.stringify(value));}).join(',')+'}}';}else{encodedScript=JSON.stringify(descriptor.script);}args=[JSON.stringify(key),encodedScript,JSON.stringify(descriptor.style),JSON.stringify(descriptor.messages),JSON.stringify(descriptor.templates)];}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-json'});return;}src='mw.loader.implement('+args.join(',')+');';if(src.length>this.MODULE_SIZE_MAX){return;}this.items[key]=src;},prune:function(){var key,module;for(key in this.items){module=key.slice(0,key.indexOf('@'));if(getModuleKey(module)!==key){this.stats.expired++;delete this.items[key];}else if(this.items[key].length>this.MODULE_SIZE_MAX){delete this.items[key];}}},clear:function(){this.items={};try{localStorage.removeItem(this.getStoreKey());}catch(e){}},requestUpdate:(function(){var hasPendingWrites=!1;function flushWrites(){var data,key;mw.loader.store.prune();while(mw.loader.store.queue.length){mw.loader.store.set(mw.loader.
store.queue.shift());}key=mw.loader.store.getStoreKey();try{localStorage.removeItem(key);data=JSON.stringify(mw.loader.store);localStorage.setItem(key,data);}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-update'});}hasPendingWrites=!1;}function onTimeout(){mw.requestIdleCallback(flushWrites);}return function(){if(!hasPendingWrites){hasPendingWrites=!0;setTimeout(onTimeout,2000);}};}())}};}()),user:{options:new Map(),tokens:new Map()},widgets:{}};window.mw=window.mediaWiki=mw;}());(function(){var maxBusy=50;mw.requestIdleCallbackInternal=function(callback){setTimeout(function(){var start=mw.now();callback({didTimeout:!1,timeRemaining:function(){return Math.max(0,maxBusy-(mw.now()-start));}});},1);};mw.requestIdleCallback=window.requestIdleCallback?window.requestIdleCallback.bind(window):mw.requestIdleCallbackInternal;}());(function(){mw.config=new mw.Map(true);mw.loader.addSource({"local":"/load.php"});mw.loader.register([[
"skins.timeless","0jgcqkx"],["skins.timeless.misc","04lmvpg"],["skins.timeless.mobile","0d3cjjj"],["skins.vector.styles","1l79kuk"],["skins.vector.styles.responsive","0iuwxxp"],["ext.cite.styles","1evyjle"],["ext.cite.style","03vcvp9"],["skins.minerva.base.styles","1sdx8gm"],["skins.minerva.content.styles","0fpo3d6"],["skins.minerva.content.styles.images","1q51m63"],["skins.minerva.icons.loggedin","19jg297"],["skins.minerva.amc.styles","0pqhg32"],["skins.minerva.icons.images","0nbnb03"],["skins.minerva.icons.images.scripts","07j6l8d",[14,16,17,15]],["skins.minerva.icons.images.scripts.misc","1eqbaf0"],["skins.minerva.icons.page.issues.uncolored","16acnez"],["skins.minerva.icons.page.issues.default.color","1o0znm7"],["skins.minerva.icons.page.issues.medium.color","1k6e8lt"],["skins.minerva.mainPage.styles","1vjqnxo"],["skins.minerva.userpage.icons","0n4ugq5"],["skins.minerva.userpage.styles","1pb5ztd"],["skins.minerva.mainMenu.icons","0akkg2a"],["skins.minerva.mainMenu.styles","1bj0tro"
],["skins.minerva.loggedin.styles","1pf97vh"],["skins.minerva.scripts","0zl45zn",[85,40,13,21,22]],["skins.minerva.notifications.badge","0h6uv4n",[41]],["skins.minerva.notifications","02ul2ah",[181,25,24]],["skins.minerva.options.share.icon","1ai65tc"],["skins.minerva.options","1xt666n",[27,24]],["skins.minerva.talk","0co02m5",[24]],["skins.minerva.toggling","0oaddwy",[24]],["skins.minerva.watchstar","0vr67yj",[24]],["mobile.pagelist.styles","1xx84em"],["mobile.pagesummary.styles","1dpao1j"],["mobile.startup.images.variants","01b5vjc"],["mobile.messageBox.styles","0ahzuor"],["mobile.userpage.icons","0lfe2sl"],["mobile.userpage.styles","0lv475s"],["mediawiki.template.hogan","05pvexc",[102]],["mobile.startup.images","1a4qpyk"],["mobile.init","0atp1hd",[134,141,41]],["mobile.startup","02dlim8",[98,153,124,210,38,182,184,135,137,35,32,33,39,34]],["mobile.editor.overlay","1ikzp7l",[107,142,123,183,188,43,41,212,226]],["mobile.editor.images","03uo3lg"],["mobile.talk.overlays","0o3qrn5",[181,
42]],["mobile.mediaViewer","18pm7rm",[41]],["mobile.categories.overlays","0mw9xhr",[42]],["mobile.languages.structured","0z592dy",[41]],["mobile.nearby.images","09iftnb"],["mobile.mainpage.css","0zfhiy0"],["mobile.site","12nwyp0",[51]],["mobile.site.styles","178jhq8"],["mobile.special.styles","0385dj2"],["mobile.special.user.icons","0mco5rr"],["mobile.special.watchlist.scripts","0rwu31l",[41]],["mobile.special.mobilecite.styles","00173vu"],["mobile.special.mobilemenu.styles","17lae9q"],["mobile.special.mobileoptions.styles","1x03hya"],["mobile.special.mobileoptions.scripts","1euv7ap",[41]],["mobile.special.nearby.styles","0ekafum"],["mobile.special.userlogin.scripts","11ahr9b"],["mobile.special.nearby.scripts","0kpe1qd",[134,48,59,41]],["mobile.special.history.styles","1bonhld"],["mobile.special.uploads.scripts","02s9rdh",[41]],["mobile.special.uploads.styles","0rg7fiz"],["mobile.special.pagefeed.styles","01xchiz"],["mobile.special.mobilediff.images","0dhvl4x"],[
"mobile.special.mobilediff.scripts","1yhlqyi",[66,41]],["mobile.special.mobilediff.styles","1anvgmv"],["user.groups","07j6l8d",[70]],["user","0k1cuul",[],"user"],["user.styles","08fimpv",[],"user"],["user.defaults","039ms66"],["user.options","0r5ungb",[72],"private"],["user.tokens","0tffind",[],"private"],["mediawiki.skinning.interface","0lhkg7k"],["jquery.makeCollapsible.styles","10guu2q"],["mediawiki.skinning.content.parsoid","0nmd691"],["jquery","0gmhg1u"],["mediawiki.base","0cjjt0t",[78]],["mediawiki.legacy.wikibits","05hpy57",[78]],["jquery.accessKeyLabel","0oqf3kq",[84,127]],["jquery.byteLength","1mvezut",[128]],["jquery.checkboxShiftClick","0m21x1o"],["jquery.client","1nc40rm"],["jquery.cookie","12o00nd"],["jquery.getAttrs","0bcjlvq"],["jquery.highlightText","0ozekmh",[127]],["jquery.i18n","0yrugds",[152]],["jquery.lengthLimit","0tb63qr",[128]],["jquery.makeCollapsible","04catal",[76]],["jquery.mw-jump","1szw96f"],["jquery.qunit","11kof1g"],["jquery.spinner","0bx0qb7"],[
"jquery.suggestions","1h5cs8k",[87]],["jquery.tablesorter","0ifcua4",[96,127,154]],["jquery.tablesorter.styles","19quk89"],["jquery.textSelection","13js4wb",[84]],["jquery.throttle-debounce","06eecyr"],["jquery.ui.position","0c81it6",[],"jquery.ui"],["jquery.ui.widget","0ve45kp",[],"jquery.ui"],["moment","07y259b",[127,150]],["mediawiki.template","0tqh6fm"],["mediawiki.template.mustache","1cv07if",[102]],["mediawiki.template.regexp","1ppu9k0",[102]],["mediawiki.apipretty","0jvrtjl"],["mediawiki.api","1b344s2",[131,74]],["mediawiki.confirmCloseWindow","0u2pg9b"],["mediawiki.diff.styles","0gdbsal"],["mediawiki.feedback","1bcwdnf",[121,218]],["mediawiki.ForeignApi","0451utn",[111]],["mediawiki.ForeignApi.core","1hj6uoc",[106,209]],["mediawiki.helplink","1390usa"],["mediawiki.hlist","1klg3o4"],["mediawiki.htmlform","1nyprc0",[89,127]],["mediawiki.htmlform.checker","03n31dt",[98]],["mediawiki.htmlform.ooui","0qx7he6",[213]],["mediawiki.htmlform.styles","1eiy1vn"],[
"mediawiki.htmlform.ooui.styles","07pqxak"],["mediawiki.icon","0r30c5u"],["mediawiki.inspect","0cq1qr4",[127,128]],["mediawiki.messagePoster","0l54pox",[110]],["mediawiki.messagePoster.wikitext","1xodl3v",[121]],["mediawiki.notification","1jy10x8",[136,143]],["mediawiki.notify","08ef6pm"],["mediawiki.notification.convertmessagebox","1udpxkk",[123]],["mediawiki.notification.convertmessagebox.styles","0nmyk2k"],["mediawiki.RegExp","0kzono7"],["mediawiki.String","17b69dq"],["mediawiki.searchSuggest","17szeuy",[86,94,106,73]],["mediawiki.storage","0b8j8oc"],["mediawiki.Title","16sfpsg",[128,136]],["mediawiki.toc","0qkz9yq",[140]],["mediawiki.toc.styles","1u9se14"],["mediawiki.Uri","0dukcku",[136,104]],["mediawiki.user","05zjnmc",[106,130,73]],["mediawiki.util","1xx5xf2",[81]],["mediawiki.viewport","06gdr2b"],["mediawiki.checkboxtoggle","00w9tlo"],["mediawiki.checkboxtoggle.styles","1u6gth1"],["mediawiki.cookie","09u66sb",[85]],["mediawiki.experiments","0rgmhag"],[
"mediawiki.editfont.styles","1v932bw"],["mediawiki.visibleTimeout","0tu6f3n"],["mediawiki.action.edit.styles","1uikh5g"],["mediawiki.action.history.styles","0gg1cxr"],["mediawiki.action.view.categoryPage.styles","1ntkbyg"],["mediawiki.action.view.redirect","1dnfl8b",[84]],["mediawiki.action.view.redirectPage","0tq2qqz"],["mediawiki.action.edit.editWarning","1mlx997",[97,107,153]],["mediawiki.language","1ukwhr1",[151]],["mediawiki.cldr","0nvnuvm",[152]],["mediawiki.libs.pluralruleparser","012f438"],["mediawiki.jqueryMsg","1kfn8do",[150,136,73]],["mediawiki.language.months","02frxri",[150]],["mediawiki.language.names","1g2jhvi",[150]],["mediawiki.language.specialCharacters","090li4z",[150]],["mediawiki.libs.jpegmeta","0ete22r"],["mediawiki.page.gallery.styles","1a3jblv"],["mediawiki.page.ready","1k6p36m",[81,83]],["mediawiki.page.startup","0xzy2gc"],["mediawiki.interface.helpers.styles","0z918tt"],["mediawiki.special","0437jdg"],["mediawiki.special.apisandbox","1xkajzx",[90,106,153,192,
212]],["mediawiki.special.block","12ewxt9",[114,189,203,196,204,201,226]],["mediawiki.special.changecredentials.js","0yzqcla",[106,116]],["mediawiki.special.changeslist","00028c6"],["mediawiki.special.changeslist.legend","1p9x93p"],["mediawiki.special.changeslist.legend.js","01hofsk",[90,140]],["mediawiki.special.preferences.ooui","0uz61qs",[107,142,125,130,196]],["mediawiki.special.preferences.styles.ooui","16iab9m"],["mediawiki.special.recentchanges","057bqh5"],["mediawiki.special.revisionDelete","1r8d2jb",[89]],["mediawiki.special.search.commonsInterwikiWidget","1wauad2",[134,106,153]],["mediawiki.special.search.interwikiwidget.styles","0sy2v3b"],["mediawiki.special.search.styles","1ujwnak"],["mediawiki.special.userlogin.common.styles","01frkh5"],["mediawiki.legacy.shared","019m1wm"],["mediawiki.ui","0fdn4xz"],["mediawiki.ui.checkbox","16waqko"],["mediawiki.ui.radio","0oyu6sq"],["mediawiki.ui.anchor","0w298fg"],["mediawiki.ui.button","0c6c1wp"],["mediawiki.ui.input","1d9kubl"],[
"mediawiki.ui.icon","04f9vqr"],["mediawiki.ui.text","01v28gi"],["mediawiki.widgets","11kig5r",[106,187,215]],["mediawiki.widgets.styles","04ic2qu"],["mediawiki.widgets.AbandonEditDialog","0hos2xs",[218]],["mediawiki.widgets.DateInputWidget","045hh3o",[190,101,215]],["mediawiki.widgets.DateInputWidget.styles","0zl919t"],["mediawiki.widgets.visibleLengthLimit","09ljyc9",[89,213]],["mediawiki.widgets.datetime","1kgxf8x",[127,213,233,234]],["mediawiki.widgets.expiry","0t9hlv6",[192,101,215]],["mediawiki.widgets.CheckMatrixWidget","19j4gxg",[213]],["mediawiki.widgets.CategoryMultiselectWidget","1h086ox",[110,215]],["mediawiki.widgets.SelectWithInputWidget","1v42u7h",[197,215]],["mediawiki.widgets.SelectWithInputWidget.styles","12dt6as"],["mediawiki.widgets.SizeFilterWidget","0on2tvb",[199,215]],["mediawiki.widgets.SizeFilterWidget.styles","05wuevv"],["mediawiki.widgets.MediaSearch","08io7zq",[110,215]],["mediawiki.widgets.UserInputWidget","06rva64",[106,215]],[
"mediawiki.widgets.UsersMultiselectWidget","1xdpsc4",[106,215]],["mediawiki.widgets.NamespacesMultiselectWidget","0z6c6d0",[215]],["mediawiki.widgets.TitlesMultiselectWidget","1vc7c96",[186]],["mediawiki.widgets.SearchInputWidget.styles","0fkv4nu"],["easy-deflate.core","06fkmhu"],["easy-deflate.deflate","18qu8bw",[206]],["easy-deflate.inflate","1y4jg3r",[206]],["oojs","17r0vy2"],["mediawiki.router","045fw5w",[211]],["oojs-router","1rw732c",[209]],["oojs-ui","07j6l8d",[217,215,218]],["oojs-ui-core","0jqbouc",[150,209,214,222,223,229,219,220]],["oojs-ui-core.styles","0j1nb08"],["oojs-ui-widgets","0r2y3iy",[213,224,233,234]],["oojs-ui-widgets.styles","1q7vxki"],["oojs-ui-toolbars","1y1a376",[213,234]],["oojs-ui-windows","1xsqwx2",[213,234]],["oojs-ui.styles.indicators","0827f70"],["oojs-ui.styles.textures","0m8y45r"],["oojs-ui.styles.icons-accessibility","0sm63on"],["oojs-ui.styles.icons-alerts","074avc8"],["oojs-ui.styles.icons-content","1esrszn"],["oojs-ui.styles.icons-editing-advanced"
,"1vkq4as"],["oojs-ui.styles.icons-editing-citation","14tlpp6"],["oojs-ui.styles.icons-editing-core","0e7ybmg"],["oojs-ui.styles.icons-editing-list","1flwgzh"],["oojs-ui.styles.icons-editing-styling","1euc2z0"],["oojs-ui.styles.icons-interactions","0h67qv9"],["oojs-ui.styles.icons-layout","1fx2qhg"],["oojs-ui.styles.icons-location","1ffoqc8"],["oojs-ui.styles.icons-media","1u5g1of"],["oojs-ui.styles.icons-moderation","0xn12ju"],["oojs-ui.styles.icons-movement","0tu14zo"],["oojs-ui.styles.icons-user","0qm4se4"],["oojs-ui.styles.icons-wikimedia","1dojucs"]]);mw.config.set({"wgLoadScript":"/load.php","debug":!1,"skin":"minerva","stylepath":"/skins","wgUrlProtocols":
"bitcoin\\:|ftp\\:\\/\\/|ftps\\:\\/\\/|geo\\:|git\\:\\/\\/|gopher\\:\\/\\/|http\\:\\/\\/|https\\:\\/\\/|irc\\:\\/\\/|ircs\\:\\/\\/|magnet\\:|mailto\\:|mms\\:\\/\\/|news\\:|nntp\\:\\/\\/|redis\\:\\/\\/|sftp\\:\\/\\/|sip\\:|sips\\:|sms\\:|ssh\\:\\/\\/|svn\\:\\/\\/|tel\\:|telnet\\:\\/\\/|urn\\:|worldwind\\:\\/\\/|xmpp\\:|\\/\\/","wgArticlePath":"/$1","wgScriptPath":"","wgScript":"/index.php","wgSearchType":null,"wgVariantArticlePath":!1,"wgActionPaths":{},"wgServer":"https://esu.wiki","wgServerName":"esu.wiki","wgUserLanguage":"zh-cn","wgContentLanguage":"zh","wgTranslateNumerals":!0,"wgVersion":"1.33.1","wgEnableAPI":!0,"wgEnableWriteAPI":!0,"wgMainPageTitle":"恶俗维基:首页","wgFormattedNamespaces":{"-2":"Media","-1":"Special","0":"","1":"Talk","2":"User","3":"User talk","4":"恶俗维基","5":"恶俗维基 talk","6":"File","7":"File talk","8":"MediaWiki","9":"MediaWiki talk","10":"Template","11":"Template talk","12":"Help","13":"Help talk","14":"Category","15":
"Category talk","828":"模块","829":"模块讨论"},"wgNamespaceIds":{"media":-2,"special":-1,"":0,"talk":1,"user":2,"user_talk":3,"恶俗维基":4,"恶俗维基_talk":5,"file":6,"file_talk":7,"mediawiki":8,"mediawiki_talk":9,"template":10,"template_talk":11,"help":12,"help_talk":13,"category":14,"category_talk":15,"模块":828,"模块讨论":829,"媒体":-2,"媒體":-2,"特殊":-1,"对话":1,"對話":1,"讨论":1,"討論":1,"用户":2,"用戶":2,"用户对话":3,"用戶對話":3,"用户讨论":3,"用戶討論":3,"图像":6,"圖像":6,"档案":6,"檔案":6,"文件":6,"图像对话":7,"圖像對話":7,"图像讨论":7,"圖像討論":7,"档案对话":7,"檔案對話":7,"档案讨论":7,"檔案討論":7,"文件对话":7,"文件對話":7,"文件讨论":7,"文件討論":7,"模板":10,"样板":10,"樣板":10,"模板对话":11,"模板對話":11,"模板讨论":11,"模板討論":11,"样板对话":11,"樣板對話":11,"样板讨论":11,"樣板討論":11,"帮助":12,"幫助":12,"帮助对话":13
,"幫助對話":13,"帮助讨论":13,"幫助討論":13,"分类":14,"分類":14,"分类对话":15,"分類對話":15,"分类讨论":15,"分類討論":15,"image":6,"image_talk":7,"媒体文件":-2,"恶俗维基讨论":5,"mediawiki讨论":9,"恶俗维基討論":5,"mediawiki討論":9,"模組":828,"模組討論":829,"使用者":2,"使用者討論":3,"使用說明":12,"使用說明討論":13,"portal":0,"project":4,"project_talk":5,"module":828,"module_talk":829},"wgContentNamespaces":[0],"wgSiteName":"恶俗维基","wgDBname":"esuwiki","wgExtraSignatureNamespaces":[],"wgExtensionAssetsPath":"/extensions","wgCookiePrefix":"esuwiki_esu_","wgCookieDomain":"","wgCookiePath":"/","wgCookieExpiration":2592000,"wgCaseSensitiveNamespaces":[],"wgLegalTitleChars":" %!\"$\u0026'()*,\\-./0-9:;=?@A-Z\\\\\\^_`a-z~+\\u0080-\\uFFFF","wgIllegalFileChars":":/\\\\","wgResourceLoaderStorageVersion":1,"wgResourceLoaderStorageEnabled":!0,"wgForeignUploadTargets":["local"],"wgEnableUploads":!1,
"wgCommentByteLimit":null,"wgCommentCodePointLimit":500,"wgCiteVisualEditorOtherGroup":!1,"wgCiteResponsiveReferences":!0,"wgMinervaSchemaMainMenuClickTrackingSampleRate":0,"wgMinervaABSamplingRate":0,"wgMinervaCountErrors":!1,"wgMinervaErrorLogSamplingRate":0,"wgMinervaReadOnly":!1});var queue=window.RLQ;window.RLQ=[];RLQ.push=function(fn){if(typeof fn==='function'){fn();}else{RLQ[RLQ.length]=fn;}};while(queue&&queue[0]){RLQ.push(queue.shift());}window.NORLQ={push:function(){}};}());}
