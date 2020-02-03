/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	config.width = '100%';

	config.uiColor = '#D4D4D4'; //#599659

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker', 'document' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'others',       groups: [ 'mode', 'doctools'] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about', groups: [ 'tools', 'about']}
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard toolbar.
	config.removeButtons = 'Subscript,Superscript,Font,Flash';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';

	// Allow to insert CSS styles inside CKEditor (setting to 'true' stops all content-filtering)
	config.allowedContent = true; 

	// AutoParagraph inserts markup when you add new lines. Setting this to false can cause usability problems and invalid html markup in your page.
	//	config.autoParagraph = false; 

	// List of font-sizes to appear in the font combo box
	config.fontSize_sizes =  '8/8px;9/9px;10/10px;11/11px;12/12px;13/13px;14/14px;15/15px;16/16px;17/17px;18/18px;19/19px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px';

};
