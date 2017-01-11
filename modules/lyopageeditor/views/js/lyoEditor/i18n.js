/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */
function _(clef) {
	// Si la traduction existe
	try {
		// Recuperation de la chaine correspondant à la clef
	    if(typeof(i18n) != 'undefined'){
    		var str = i18n[clef];
    		// Si la traduction n'existe pas
    		if (str === undefined) {
    			// Renvoi la clef
    			str = clef;
    			// Dans le cadre du développement,
    			// une notification peut être ajouté.
    			if(LyoEditor.isDebug) window.console.log("i18n['" + clef  +"']='';");
    		};
    		// Renvoi la chaine traduite
    		return str;
	    }else{
	        return clef;
	    }
	} catch (e) {
		// Si il y a un problème (fichier non chargé par exemple),
		// renvoi la clée
		return clef;
	};
};