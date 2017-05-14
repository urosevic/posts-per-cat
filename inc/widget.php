<?php

class PPC_Widget extends WP_Widget {

	public function __construct() {
		// widget actual processes
		parent::__construct(
			'ppc_widget', // Base ID
			__('Posts per Cat', 'ppc'), // Name
			array( 'description' => __( 'Widget for Posts-per-Cat block in custom Widget Area', 'ppc' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		$title = apply_filters( 'widget_title', $instance['title'] );

		$options = array(
			'columns'  => $instance['columns'],
			'minh'     => $instance['minh'],
			'parent'   => $instance['parent'],
			'order'    => $instance['order'],
			'catonly'  => $instance['catonly'],
			'noctlink' => $instance['noctlink'],
			'more'     => $instance['more'],
			'moretxt'  => $instance['moretxt'],
			'posts'    => (empty($instance['posts']))?5:$instance['posts'],
			'porderby' => (empty($instance['porderby']))?'date':$instance['porderby'],
			'porder'   => (empty($instance['porder']))?'DESC':$instance['porder'],
			'titlelen' => (empty($instance['titlelen']))?'':$instance['titlelen'],
			'shorten'  => $instance['shorten'],
			'commnum'  => $instance['commnum'],
			'nosticky' => $instance['nosticky'],
			'excerpts' => $instance['excerpts'],
			'content'  => $instance['content'],
			'excleng'  => (empty($instance['excleng']))?'':$instance['excleng'],
			'thumb'    => (empty($instance['thumb']))?false:$instance['thumb'],
			'tsize'    => $instance['tsize']
		);
		if ( !empty($instance['include']) ) $options['include'] = $instance['include'];
		if ( !empty($instance['exclude']) ) $options['exclude'] = $instance['exclude'];

		$template = !empty($instance['template']) ? $instance['template'] : null;
		$out = $args['before_widget'];
		if ( ! empty( $title ) )
			$out .= $args['before_title'] . $title . $args['after_title'];

		ob_start();
		echo POSTS_PER_CAT::shortcode($options, $template);
		$out .= ob_get_clean();

		$out .= $args['after_widget'];
		echo $out;
	}

 	public function form( $instance ) {
		// outputs the options form on admin
		$options  = get_option('postspercat');;
		$title    = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'Posts per Category', 'ppc' );
		$minh     = ( isset( $instance[ 'minh' ] ) ) ? $instance[ 'minh' ] : '';
		$include  = ( isset( $instance[ 'include' ] ) ) ? $instance[ 'include' ] : '';
		$exclude  = ( isset( $instance[ 'exclude' ] ) ) ? $instance[ 'exclude' ] : '';
		$parent   = ( isset( $instance[ 'parent' ] ) ) ? $instance[ 'parent' ] : false;
		$order    = ( isset( $instance[ 'order' ] ) ) ? $instance[ 'order' ] : 'ID';
		$catonly  = ( isset( $instance[ 'catonly' ] ) ) ? $instance[ 'catonly' ] : false;
		$noctlink = ( isset( $instance[ 'noctlink' ] ) ) ? $instance[ 'noctlink' ] : false;
		$more     = ( isset( $instance[ 'more' ] ) ) ? $instance[ 'more' ] : false;
		$moretxt  = ( isset( $instance[ 'moretxt' ] ) ) ? $instance[ 'moretxt' ] : false;
		$posts    = ( isset( $instance[ 'posts' ] ) ) ? $instance[ 'posts' ] : "5";
		$porderby = ( isset( $instance[ 'porderby' ] ) ) ? $instance[ 'porderby' ] : "date";
		$porder   = ( isset( $instance[ 'porder' ] ) ) ? $instance[ 'porder' ] : "DESC";
		$titlelen = ( isset( $instance[ 'titlelen' ] ) ) ? $instance[ 'titlelen' ] : '';
		$shorten  = ( isset( $instance[ 'shorten' ] ) ) ? $instance[ 'shorten' ] : false;
		$commnum  = ( isset( $instance[ 'commnum' ] ) ) ? $instance[ 'commnum' ] : false;
		$nosticky = ( isset( $instance[ 'nosticky' ] ) ) ? $instance[ 'nosticky' ] : false;
		$excerpts = ( isset( $instance[ 'excerpts' ] ) ) ? $instance[ 'excerpts' ] : 'none';
		$content  = ( isset( $instance[ 'content' ] ) ) ? $instance[ 'content' ] : false;
		$excleng  = ( isset( $instance[ 'excleng' ] ) ) ? $instance[ 'excleng' ] : '';
		$thumb    = ( isset( $instance[ 'thumb' ] ) ) ? $instance[ 'thumb' ] : false;
		$tsize    = ( isset( $instance[ 'tsize' ] ) ) ? $instance[ 'tsize' ] : '60';
		$columns  = ( isset( $instance[ 'columns' ] ) ) ? $instance[ 'columns' ] : '2';
		$template = ( isset( $instance[ 'template' ] ) ) ? $instance[ 'template' ] : '';

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>">Number of columns</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>">
				<option value="1"<?php selected( $columns, '1' ); ?>><?php _e('One column per row (full width)', 'ppc'); ?></option>
				<option value="2"<?php selected( $columns, '2' ); ?>><?php _e('Two columns per row', 'ppc'); ?></option>
				<option value="3"<?php selected( $columns, '3' ); ?>><?php _e('Three columns per row', 'ppc'); ?></option>
				<option value="4"<?php selected( $columns, '4' ); ?>><?php _e('Four columns per row', 'ppc'); ?></option>
				<option value="5"<?php selected( $columns, '5' ); ?>><?php _e('Five columns per row', 'ppc'); ?></option>
			</select>
		</p>

		<p>
			<label>Minimal height of box</label><br />
			<input class="small-text" id="<?php echo $this->get_field_id( 'minh' ); ?>" name="<?php echo $this->get_field_name( 'minh' ); ?>" type="number" value="<?php echo esc_attr( $minh ); ?>"  title="<?php _e("(leave empty to disable min-height)","ppc"); ?>" /> px
		</p>
		<h3>Categories</h3>
		<p>
			<label>Include category</label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" type="text" value="<?php echo esc_attr( $include ); ?>"  title="<?php _e("comma separated category ID's","ppc"); ?>" />
		</p>

		<p>
			<label>Exclude category</label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>" title="<?php _e("comma separated category ID's","ppc"); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $parent, true ); ?> id="<?php echo $this->get_field_id( 'parent' ); ?>" name="<?php echo $this->get_field_name( 'parent' ); ?>" /> <label for="<?php echo $this->get_field_id( 'parent' ); ?>"><?php _e('Only top level categories', 'ppc'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>">Order categories by</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<option value="ID"<?php selected( $order, 'ID' ); ?>><?php _e('Category ID', 'ppc'); ?></option>
				<option value="name"<?php selected( $order, 'name' ); ?>><?php _e('Category Name', 'ppc'); ?></option>
				<option value="custom"<?php selected( $order, 'custom' ); ?>><?php _e('Custom, as listed in Include category', 'ppc'); ?></option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $catonly, true ); ?> id="<?php echo $this->get_field_id( 'catonly' ); ?>" name="<?php echo $this->get_field_name( 'catonly' ); ?>" title="<?php _e("exclude categories different from currently displayed on category archive and ignore first category rules on category archive","ppc"); ?>" /> <label for="<?php echo $this->get_field_id( 'catonly' ); ?>"><?php _e('Only from displayed category archive', 'ppc'); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $noctlink, true ); ?> id="<?php echo $this->get_field_id( 'noctlink' ); ?>" name="<?php echo $this->get_field_name( 'noctlink' ); ?>" title="<?php _e("leave unchecked to link category title to archive","ppc"); ?>" /> <label for="<?php echo $this->get_field_id( 'noctlink' ); ?>"><?php _e('Do not link category name', 'ppc'); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $more, true ); ?> id="<?php echo $this->get_field_id( 'more' ); ?>" name="<?php echo $this->get_field_name( 'more' ); ?>" title="<?php _e('check to print "read more" link bellow list of headlines',"ppc"); ?>" /> <label for="<?php echo $this->get_field_id( 'more' ); ?>"><?php _e('Standalone link to archives', 'ppc'); ?></label>
		</p>

		<p>
			<label>Archive link prefix</label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'moretxt' ); ?>" name="<?php echo $this->get_field_name( 'moretxt' ); ?>" type="text" value="<?php echo esc_attr( $moretxt ); ?>" />
		</p>

		<h3>Headlines</h3>

		<p>
			<label for="<?php echo $this->get_field_id( 'porderby' ); ?>">Sort retrieved posts</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'porderby' ); ?>" name="<?php echo $this->get_field_name( 'porderby' ); ?>">
				<option value="none"<?php selected( $porderby, 'none' ); ?>><?php _e("No order", 'ppc'); ?></option>
				<option value="ID"<?php selected( $porderby, 'ID' ); ?>><?php _e('ID', 'ppc'); ?></option>
				<option value="author"<?php selected( $porderby, 'author' ); ?>><?php _e('Author', 'ppc'); ?></option>
				<option value="title"<?php selected( $porderby, 'title' ); ?>><?php _e('Title', 'ppc'); ?></option>
				<option value="name"<?php selected( $porderby, 'name' ); ?>><?php _e('Post slug', 'ppc'); ?></option>
				<option value="date"<?php selected( $porderby, 'date' ); ?>><?php _e('Publishing date', 'ppc'); ?></option>
				<option value="modified"<?php selected( $porderby, 'modified' ); ?>><?php _e('Last modified date', 'ppc'); ?></option>
				<option value="comment_count"<?php selected( $porderby, 'comment_count' ); ?>><?php _e('Number of comments', 'ppc'); ?></option>
				<option value="rand"<?php selected( $porderby, 'rand' ); ?>><?php _e('Random', 'ppc'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'porder' ); ?>">Sorting order</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'porder' ); ?>" name="<?php echo $this->get_field_name( 'porder' ); ?>">
				<option value="DESC"<?php selected( $porder, 'DESC' ); ?>><?php _e("Descending", 'ppc'); ?></option>
				<option value="ASC"<?php selected( $porder, 'ASC' ); ?>><?php _e('Ascending', 'ppc'); ?></option>
			</select>
		</p>
		<p>
			<label>Number of headlines</label><br />
			<input class="small-text" id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" type="number" value="<?php echo esc_attr( $posts ); ?>" />
		</p>

		<p>
			<label>Headline length</label><br />
			<input class="small-text" id="<?php echo $this->get_field_id( 'titlelen' ); ?>" name="<?php echo $this->get_field_name( 'titlelen' ); ?>" type="number" value="<?php echo esc_attr( $titlelen ); ?>" title="<?php _e("leave blank for full post title length, optimal 34 characters","ppc"); ?>" /> characters
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $shorten, true ); ?> id="<?php echo $this->get_field_id( 'shorten' ); ?>" name="<?php echo $this->get_field_name( 'shorten' ); ?>" /> <label for="<?php echo $this->get_field_id( 'shorten' ); ?>"><?php _e('Shorten headline', 'ppc'); ?> [*]</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $commnum, true ); ?> id="<?php echo $this->get_field_id( 'commnum' ); ?>" name="<?php echo $this->get_field_name( 'commnum' ); ?>" /> <label for="<?php echo $this->get_field_id( 'commnum' ); ?>"><?php _e('Display comment number', 'ppc'); ?> [*]</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $nosticky, true ); ?> id="<?php echo $this->get_field_id( 'nosticky' ); ?>" name="<?php echo $this->get_field_name( 'nosticky' ); ?>" /> <label for="<?php echo $this->get_field_id( 'nosticky' ); ?>"><?php _e('Hide sticky posts', 'ppc'); ?></label>
		</p>

		<h3>Content</h3>
		<p>
			<label for="<?php echo $this->get_field_id( 'excerpts' ); ?>">Show excerpt [*]</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'excerpts' ); ?>" name="<?php echo $this->get_field_name( 'excerpts' ); ?>">
				<option value="none"<?php selected( $excerpts, 'none' ); ?>><?php _e("Don't display", 'ppc'); ?></option>
				<option value="first"<?php selected( $excerpts, 'first' ); ?>><?php _e('For first article only', 'ppc'); ?></option>
				<option value="all"<?php selected( $excerpts, 'all' ); ?>><?php _e('For all articles', 'ppc'); ?></option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $content, true ); ?> id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" title="<?php _e('use post content in stead of post excerpt','ppc');?>" /> <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e('Use post content as excerpt', 'ppc'); ?></label>
		</p>

		<p>
			<label>Excerpt length</label><br />
			<input class="small-text" id="<?php echo $this->get_field_id( 'excleng' ); ?>" name="<?php echo $this->get_field_name( 'excleng' ); ?>" type="number" value="<?php echo esc_attr( $excleng ); ?>" title="<?php _e("leave empty for full excerpt length","ppc"); ?>" /> characters
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php 
			checked( (bool) $thumb, true ); 
			?> id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" title="<?php _e('thumbnail is shown only if theme support it, and excerpt is enabled','ppc');?>" /> <label for="<?php echo $this->get_field_id( 'thumb' ); ?>"><?php _e('Show thumbnail with excerpt', 'ppc'); ?> [*]</label>
		</p>
		<p>
			<label>Thumbnail size</label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'tsize' ); ?>" name="<?php echo $this->get_field_name( 'tsize' ); ?>" type="text" value="<?php echo esc_attr( $tsize ); ?>" title="<?php _e("enter size in px for thumbnail width (height is same) or WxH or image size name (thumbnail, medium, large, full)","ppc"); ?>" />
			<small>Enter only WIDTH in pixels for square; WIDTHxHEIGHT or image size name (thumbnail, small, mediaum, large, full, etc) for custom aspect ratio</small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'template' ); ?>">Single line HTML template</label>
			<textarea class="widefat" rows="5" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" title="<?php _e('custom HTML syntax for single post in box','ppc');?>"><?php echo esc_attr( $template ); ?></textarea>
			<small>Options with <em>[*]</em> at the end of label will not affect template</small>
		</p>

		<?php 
	} // form()

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance             = array();
		$options              = get_option('postspercat');
		// main
		$instance['title']    = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['columns']  = (!empty($new_instance['columns'])) ? strip_tags($new_instance['columns']) : '';
		$instance['minh']     = (!empty($new_instance['minh'])) ? strip_tags($new_instance['minh']) : '';
		// categories
		$instance['include']  = (!empty($new_instance['include'])) ? strip_tags($new_instance['include']) : '';
		$instance['exclude']  = (!empty($new_instance['exclude'])) ? strip_tags($new_instance['exclude']) : '';
		$instance['parent']   = (!empty($new_instance['parent'])) ? strip_tags($new_instance['parent']) : '';
		$instance['order']    = (!empty($new_instance['order'])) ? strip_tags($new_instance['order']) : '';
		$instance['catonly']  = (!empty($new_instance['catonly'])) ? strip_tags($new_instance['catonly']) : '';
		$instance['noctlink'] = (!empty($new_instance['noctlink'])) ? strip_tags($new_instance['noctlink']) : '';
		$instance['more']     = (!empty($new_instance['more'])) ? strip_tags($new_instance['more']) : '';
		$instance['moretxt']  = (!empty($new_instance['moretxt'])) ? strip_tags($new_instance['moretxt']) : 'More from';
		// Headlines
		$instance['porderby']  = (!empty($new_instance['porderby'])) ? strip_tags($new_instance['porderby']) : 'date';
		$instance['porder']  = (!empty($new_instance['porder'])) ? strip_tags($new_instance['porder']) : 'DESC';
		$instance['posts']  = (!empty($new_instance['posts'])) ? strip_tags($new_instance['posts']) : '';
		$instance['titlelen']  = (!empty($new_instance['titlelen'])) ? strip_tags($new_instance['titlelen']) : '';
		$instance['shorten']  = (!empty($new_instance['shorten'])) ? strip_tags($new_instance['shorten']) : '';
		$instance['commnum']  = (!empty($new_instance['commnum'])) ? strip_tags($new_instance['commnum']) : '';
		$instance['nosticky'] = (!empty($new_instance['nosticky'])) ? strip_tags($new_instance['nosticky']) : '';
		// Content
		$instance['excerpts'] = (!empty($new_instance['excerpts'])) ? strip_tags($new_instance['excerpts']) : 'none';
		$instance['content']  = (!empty($new_instance['content'])) ? strip_tags($new_instance['content']) : '';
		$instance['excleng']  = (!empty($new_instance['excleng'])) ? strip_tags($new_instance['excleng']) : '';
		$instance['thumb']    = (!empty($new_instance['thumb'])) ? strip_tags($new_instance['thumb']) : '';
		$instance['tsize']    = (!empty($new_instance['tsize'])) ? strip_tags($new_instance['tsize']) : '60';
		$instance['template']    = (!empty($new_instance['template'])) ? $new_instance['template'] : '';
		return $instance;
	} // update()
} // end class

// register Foo_Widget widget
function register_ppc_widget() {
    register_widget( 'PPC_Widget' );
}
add_action( 'widgets_init', 'register_ppc_widget' );
