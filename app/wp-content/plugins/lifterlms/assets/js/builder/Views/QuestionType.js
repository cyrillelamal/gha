/**
 * Question Type View
 *
 * @since 3.16.0
 * @since 3.30.1 Fixed issue causing multiple binds for add_existing_question events.
 * @version 5.4.0
 */
define( [ 'Views/Popover', 'Views/PostSearch' ], function( Popover, QuestionSearch ) {

	return Backbone.View.extend( {

		/**
		 * HTML class names.
		 *
		 * @type  {String}
		 */
		className: 'llms-question-type',

		events: {
			'click .llms-add-question': 'add_question',
		},

		/**
		 * HTML element wrapper ID attribute.
		 *
		 * @since 3.16.0
		 *
		 * @return {String}
		 */
		id: function() {
			return 'llms-question-type-' + this.model.id;
		},

		/**
		 * Wrapper Tag name.
		 *
		 * @type {String}
		 */
		tagName: 'li',

		/**
		 * Get the underscore template.
		 *
		 * @type {[type]}
		 */
		template: wp.template( 'llms-question-type-template' ),

		/**
		 * Initialization callback func (renders the element on screen).
		 *
		 * @since 3.16.0
		 *
		 * @return {Void}
		 */
		initialize: function() {

			this.render();

		},

		/**
		 * Compiles the template and renders the view.
		 *
		 * @since 3.16.0
		 *
		 * @return {Self} For chaining.
		 */
		render: function() {
			this.$el.html( this.template( this.model ) );
			return this;
		},

		/**
		 * Add a question of the selected type to the current quiz.
		 *
		 * @since 3.16.0
		 * @since 3.27.0 Unknown.
		 *
		 * @return {Void}
		 */
		add_question: function() {

			if ( 'existing' === this.model.get( 'id' ) ) {
				this.add_existing_question_click();
			} else {
				this.add_new_question();
			}

		},

		/**
		 * Add a new question to the quiz.
		 *
		 * @since 3.27.0
		 * @since 3.30.1 Fixed issue causing multiple binds.
		 *
		 * @return {Void}
		 */
		add_existing_question_click: function() {

			var pop = new Popover( {
				el: '#llms-add-question--existing',
				args: {
					backdrop: true,
					closeable: true,
					container: '#llms-builder-sidebar',
					dismissible: true,
					placement: 'top-left',
					width: 'calc( 100% - 40px )',
					offsetLeft: 250,
					offsetTop: 60,
					title: LLMS.l10n.translate( 'Add Existing Question' ),
					content: new QuestionSearch( {
						post_type: 'llms_question',
						searching_message: LLMS.l10n.translate( 'Search for existing questions...' ),
					} ).render().$el,
				}
			} );

			pop.show();
			Backbone.pubSub.on( 'question-search-select', this.add_existing_question, this );
			Backbone.pubSub.on( 'question-search-select', function( event ) {
				pop.hide();
				Backbone.pubSub.off( 'question-search-select', this.add_existing_question, this );
			}, this );

		},

		/**
		 * Callback event fired when a question is selected from the Add Existing Question popover interface.
		 *
		 * @since 3.27.0
		 * @since 5.4.0 Use author id instead of the question author object.
		 *
		 * @param {Object} event JS event object.
		 * @return {Void}
		 */
		add_existing_question: function( event ) {

			var question = event.data;

			if ( 'clone' === event.action ) {
				question = _.prepareQuestionObjectForCloning( question );
			} else {
				// Use author id instead of the question author object.
				question = _.prepareExistingPostObjectDataForAddingOrCloning( question );
				question._forceSync = true;
			}

			question._expanded = true;
			this.quiz.add_question( question );

			this.quiz.trigger( 'new-question-added' );

		},

		/**
		 * Add a new question to the quiz.
		 *
		 * @since 3.27.0
		 *
		 * @return {Void}
		 */
		add_new_question: function() {

			this.quiz.add_question( {
				_expanded: true,
				choices: this.model.get( 'default_choices' ) ? this.model.get( 'default_choices' ) : null,
				question_type: this.model,
			} );

			this.quiz.trigger( 'new-question-added' );

		},

		// filter: function( term ) {

		// var words = this.model.get_keywords().map( function( word ) {
		// return word.toLowerCase();
		// } );

		// term = term.toLowerCase();

		// if ( -1 === words.indexOf( term ) ) {
		// this.$el.addClass( 'filtered' );
		// } else {
		// this.$el.removeClass( 'filtered' );
		// }

		// },

		// clear_filter: function() {
		// this.$el.removeClass( 'filtered' );
		// }

	} );

} );
