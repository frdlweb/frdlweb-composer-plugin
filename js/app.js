


(function(global){
 "use strict";

frdl.UI.defer();		
	
frdl.ready(function(){

	
  frdl.Engine.AppfrdlMyWebfan = global.angular.module('webfan.my', ['frdl']);

  		
  frdl.Engine.AppfrdlMyWebfan.controller('webfan.my.NavigationLinksCtrl', function($scope){
  	 	
    var DesktopInitiated = false;
    
    $scope.fnOpenWorkspace = function(){
    	if(true===DesktopInitiated)return frdl.wd(true);
    
      DesktopInitiated=true;

	try{
	 $.WebfanDesktop({
	 browseContentOnInit : false,	
      modules : [
        function(){
	   $.ApplicationComposerOpen({});	
	},
	function(){
		$.WebfanDesktop.resetReady('Loading...',25, 
	   	        function(){
	   	        	var r= ( 'undefined' !== typeof Dom.g('window_frdl-webfan'));
	   	        	if(true !== r) return false;
	   	        	$.WebfanDesktop.go('frdl-webfan', 'installPHARclick');
	   	        	return r;
	   	        }
	   	     );
	}
  ]
}).browseContentOnInit = false;
 

	}catch(err){
		console.error(err);
	}
     	
     


	};

    $scope.NavigationLinks = [
      { rel: "homepage", href: "http://my.webfan.de", innerHTML : "Get your free Homepage..."},
  /*    { rel: "widget", href: "http://frdl.webfan.de/site/modules/de.frdl.webfan/zip.package!", innerHTML : "Get My.webfan for you host!" }, */
      { rel: "installer", href: "http://www.webfan.de/install/", innerHTML : "Get frdl/webfan Application Composer - The PHP Package manager" } 
      
     
    ];
    
    
  })  
  ;
  
  /*
frdl.ready(function(){  
   frdl.UI.load();
});  
*/
});
		
}(typeof exports !== 'undefined' ? global : (typeof this !== 'underfined' ? this : window)));
