/**
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.php>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
var NewsEntryListEdit = Class.create({
	/**
	 * Inits NewsEntryListEdit.
	 */
	initialize: function(data, count) {
		this.data = data;
		this.count = count;
		this.options = Object.extend({
			page:			'',
			url:			'',
			categoryID:		0,
			entryID:		0,
			enableRecycleBin:	true
		}, arguments[2] || { });
		
		// get parent object
		this.parentObject = new InlineListEdit('newsEntry', this);
	},
	
	/**
	 * Initialises special news entry options.
	 */
	initItem: function(id) {
		// init subject edit
		if (permissions['canEditNewsEntry']) {
			var entrySubjectDiv = $('newsEntryTitle'+id);
			if (entrySubjectDiv) {
				entrySubjectDiv.observe('dblclick', function(id) { this.startTitleEdit(id); }.bind(this, id));
			}
		}
	},	
	
	/**
	 * Show the status of a news entry.
	 */
	showStatus: function(id) {
		var entry = this.data.get(id);
		
		// get row
		var row = $('newsEntryRow'+id);
		
		// update css class
		if (row) {
			// remove all classes
			row.removeClassName('marked');			
			row.removeClassName('disabled');
			row.removeClassName('deleted');
			
			// disabled
			if (entry.isDisabled) {
				row.addClassName('disabled');
			}
			
			// deleted
			if (entry.isDeleted) {
				row.addClassName('deleted');
			}
			
			// marked
			if (entry.isMarked) {
				row.addClassName('marked');
			}
		}
		
		// update icon
		var icon = $('newsEntryEdit'+id);
		if (icon && icon.src != undefined) {
			// deleted
			if (entry.isDeleted) {
				icon.src = icon.src.replace(/[a-z0-9-_]*?(?=(?:Options)?(?:S|M|L|XL)\.png$)/i, 'newsEntryTrash');
			}
			else {
				icon.src = icon.src.replace(/newsEntryTrash/i, 'newsEntry');
			}
		}
	},
	
	/**
	 * Saves the marked status.
	 */
	saveMarkedStatus: function(data) {
		new Ajax.Request('index.php?action=NewsEntryMark&t='+SECURITY_TOKEN+SID_ARG_2ND, {
			method: 'post',
			parameters: data
		});
	},
	
	/**
	 * Returns a list of the edit options for the edit menu.
	 */
	getEditOptions: function(id) {
		var options = new Array();
		var i = 0;
		var entry = this.data.get(id);
		
		// edit title
		if (permissions['canEditNewsEntry']) {
			options[i] = new Object();
			options[i]['function'] = 'newsEntryListEdit.startTitleEdit('+id+');';
			options[i]['text'] = language['wsip.news.entries.button.editTitle'];
			i++;
		}
			
		// enable / disable
		if (permissions['canEnableNewsEntry']) {
			if (entry.isDisabled == 1) {
				options[i] = new Object();
				options[i]['function'] = 'newsEntryListEdit.enable('+id+');';
				options[i]['text'] = language['wsip.news.entries.button.enable'];
				i++;
			}
			else if (entry.isDeleted == 0) {
				options[i] = new Object();
				options[i]['function'] = 'newsEntryListEdit.disable('+id+');';
				options[i]['text'] = language['wsip.news.entries.button.disable'];
				i++;
			}
		}
		
		// delete
		if (permissions['canDeleteNewsEntry'] && (permissions['canDeleteNewsEntryCompletely'] || (entry.isDeleted == 0 && this.options.enableRecycleBin))) {
			options[i] = new Object();
			options[i]['function'] = 'newsEntryListEdit.remove('+id+');';
			options[i]['text'] = (entry.isDeleted == 0 ? language['wcf.global.button.delete'] : language['wcf.global.button.deleteCompletely']);
			i++;
		}
			
		// recover
		if (entry.isDeleted == 1 && permissions['canDeleteNewsEntryCompletely']) {
			options[i] = new Object();
			options[i]['function'] = 'newsEntryListEdit.recover('+id+');';
			options[i]['text'] = language['wsip.news.entries.button.recover'];
			i++;
		}
			
		// marked status
		if (permissions['canMarkNewsEntry']) {
			var markedStatus = entry ? entry.isMarked : false;
			options[i] = new Object();
			options[i]['function'] = 'newsEntryListEdit.parentObject.markItem(' + (markedStatus ? 'false' : 'true') + ', '+id+');';
			options[i]['text'] = markedStatus ? language['wcf.global.button.unmark'] : language['wcf.global.button.mark'];
			i++;
		}
			
		return options;
	},

	/**
	 * Returns a list of the edit options for the edit marked menu.
	 */
	getEditMarkedOptions: function() {
		var options = new Array();
		var i = 0;
		
		if (this.options.page == 'category') {
			// move
			if (permissions['canMoveNewsEntry']) {
				options[i] = new Object();
				options[i]['function'] = "newsEntryListEdit.move('move');";
				options[i]['text'] = language['wsip.news.entries.button.move'];
				i++;
			}
		}
		
		// delete
		if (permissions['canDeleteNewsEntry'] && (permissions['canDeleteNewsEntryCompletely'] || this.options.enableRecycleBin)) {
			options[i] = new Object();
			options[i]['function'] = 'newsEntryListEdit.removeAll();';
			options[i]['text'] = language['wcf.global.button.delete'];
			i++;
		}
		
		// recover
		if (this.options.enableRecycleBin && permissions['canDeleteNewsEntryCompletely']) {
			options[i] = new Object();
			options[i]['function'] = 'newsEntryListEdit.recoverAll();';
			options[i]['text'] = language['wsip.news.entries.button.recover'];
			i++;
		}
		
		// unmark all
		options[i] = new Object();
		options[i]['function'] = 'newsEntryListEdit.unmarkAll();';
		options[i]['text'] = language['wcf.global.button.unmark'];
		i++;
		
		// show marked
		options[i] = new Object();
		options[i]['function'] = 'document.location.href = fixURL("index.php?page=ModerationMarkedEntries'+SID_ARG_2ND+'")';
		options[i]['text'] = language['wsip.news.entries.button.showMarked'];
		i++;
		
		return options;
	},
	
	/**
	 * Returns the title of the edit marked menu.
	 */
	getMarkedTitle: function() {
		return eval(language['wsip.news.entries.markedEntries']);
	},
	
	/**
	 * Moves this entry.
	 */
	move: function(action) {
		document.location.href = fixURL('index.php?action=NewsEntryMoveMarked&categoryID='+this.options.categoryID+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	},
	
	/**
	 * Deletes this entry.
	 */
	remove: function(id) {
		var entry = this.data.get(id);
		if (entry.isDeleted == 0 && this.options.enableRecycleBin) {
			var promptResult = prompt(language['wsip.news.entries.delete.reason']);
			if (typeof(promptResult) != 'object' && typeof(promptResult) != 'undefined') {
				if (permissions['canReadDeletedNewsEntry']) {
					new Ajax.Request('index.php?action=NewsEntryTrash&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
						method: 'post',
						parameters: {
							reason: promptResult
						},
						onSuccess: function() {
							entry.isDeleted = 1;
							this.showStatus(id);
							var entryRow = $('newsEntryRow'+id);
							if (entryRow) {
								entryRow.down('.editNote').insert('<p class="deleteNote smallFont">'+promptResult.escapeHTML()+'</p>');
							}
						}.bind(this)
					});
				}
				else {
					document.location.href = fixURL('index.php?action=NewsEntryTrash&entryID='+id+'&reason='+encodeURIComponent(promptResult)+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
				}
			}
		}
		else {
			if (confirm((entry.isDeleted == 0 ? language['wsip.news.entries.delete.sure'] : language['wsip.news.entries.deleteCompletely.sure']))) {
				document.location.href = fixURL('index.php?action=NewsEntryDelete&entryID='+id+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			}
		}
	},
	
	/**
	 * Deletes all marked entries.
	 */
	removeAll: function() {
		if (this.options.enableRecycleBin) {
			var promptResult = prompt(language['wsip.news.entries.deleteMarked.reason']);
			if (typeof(promptResult) != 'object' && typeof(promptResult) != 'undefined') {
				document.location.href = fixURL('index.php?action=NewsEntryDeleteMarked&reason='+encodeURIComponent(promptResult)+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
			}
		}
		else if (confirm(language['wsip.news.entries.deleteMarked.sure'])) {
			document.location.href = fixURL('index.php?action=NewsEntryDeleteMarked&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
		}
	},
	
	/**
	 * Recovers all marked entries.
	 */
	recoverAll: function(id) {
		document.location.href = fixURL('index.php?action=NewsEntryRecoverMarked&categoryID='+this.options.categoryID+'&url='+encodeURIComponent(this.options.url)+'&t='+SECURITY_TOKEN+SID_ARG_2ND);
	},
	
	/**
	 * Unmarkes all marked entries.
	 */
	unmarkAll: function() {
		new Ajax.Request('index.php?action=NewsEntryUnmarkAll&t='+SECURITY_TOKEN+SID_ARG_2ND, {
			method: 'get'
		});
		
		// checkboxes
		this.count = 0;
		var entryIDArray = this.data.keys();
		for (var i = 0; i < entryIDArray.length; i++) {
			var id = entryIDArray[i];
			var entry = this.data.get(id);
		
			entry.isMarked = 0;
			var checkbox = $('newsEntryMark'+id);
			if (checkbox) {
				checkbox.checked = false;
			}
			
			this.showStatus(id);
		}
		
		// mark all checkboxes
		this.parentObject.checkMarkAll(false);
		
		// edit marked menu
		this.parentObject.showMarked();
	},

	/**
	 * Recovers an entry.
	 */
	recover: function(id) {
		var entry = this.data.get(id);
		if (entry.isDeleted == 1) {
			new Ajax.Request('index.php?action=NewsEntryRecover&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				onSuccess: function() {
					entry.isDeleted = 0;
					this.showStatus(id);
					var entryRow = $('newsEntryRow'+id);
					if (entryRow) {
						entryRow.down('.deleteNote').remove();
					}
				}.bind(this)
			});
		}
	},
	
	/**
	 * Enables an entry.
	 */
	enable: function(id) {
		var entry = this.data.get(id);
		if (entry.isDisabled == 1) {
			new Ajax.Request('index.php?action=NewsEntryEnable&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				onSuccess: function() {
					entry.isDisabled = 0;
					this.showStatus(id);
				}.bind(this)
			});
		}
	},

	/**
	 * Disables an entry.
	 */
	disable: function(id) {
		var entry = this.data.get(id);
		if (entry.isDisabled == 0 && entry.isDeleted == 0) {
			new Ajax.Request('index.php?action=NewsEntryDisable&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				onSuccess: function() {
					entry.isDisabled = 1;
					this.showStatus(id);
				}.bind(this)
			});
		}
	},
	
	/**
	 * Starts the editing of an entry title.
	 */
	startTitleEdit: function(id) {
		if ($('newsEntryTitleInput'+id)) return;
		var entrySubjectDiv = $('newsEntryTitle'+id);
		if (entrySubjectDiv) {
			// get value and hide title
			var value = '';
			var title = entrySubjectDiv.select('a')[0];
			if (title) {
				title.addClassName('hidden');
				value = title.innerHTML.unescapeHTML();
			}
			
			// show input field
			var inputField = new Element('input', { 'id': 'newsEntryTitleInput'+id, 'type': 'text', 'className': 'inputText', 'value': value });
			entrySubjectDiv.insert(inputField);
			
			// add event listeners
			inputField.observe('keydown', function(id, e) { this.doTitleEdit(id, e); }.bind(this, id));
			inputField.observe('blur', function(id) { this.abortTitleEdit(id); }.bind(this, id));
			
			// set focus
			inputField.focus();
		}
	},
	
	/**
	 * Aborts the editing of an entry title.
	 */
	abortTitleEdit: function(id) {
		// remove input field
		var entrySubjectInputDiv = $('newsEntryTitleInput'+id);
		if (entrySubjectInputDiv) {
			entrySubjectInputDiv.remove();
		}
		
		// show title
		var entrySubjectDiv = $('newsEntryTitle'+id);
		if (entrySubjectDiv) {
			// show first child
			var title = entrySubjectDiv.select('a')[0];
			if (title) {
				title.removeClassName('hidden');
			}
		}
	},
	
	/**
	 * Takes the value of the input-field and creates an ajax-request to save the new title.
	 * enter = save
	 * esc = abort
	 */
	doTitleEdit: function(id, event) {
		var keyCode = event.keyCode;
	
		// get input field
		var inputField = $('newsEntryTitleInput'+id);
		
		// enter
		if (keyCode == Event.KEY_RETURN && inputField.value != '') {
			// set new value
			inputField.value = inputField.value.strip();
			var entrySubjectDiv = $('newsEntryTitle'+id);
			var title = entrySubjectDiv.select('a')[0];
			if (title) {
				title.update(inputField.getValue().escapeHTML());
			}
			
			// save new value
			new Ajax.Request('index.php?action=NewsEntrySubjectEdit&entryID='+id+'&t='+SECURITY_TOKEN+SID_ARG_2ND, {
				method: 'get',
				parameters: {
					subject: inputField.getValue()
				}
			});
			
			// abort editing
			inputField.blur();
			return false;
		}
		// esc
		else if (keyCode == '27') {
			inputField.blur();
			return false;
		}
	}
});