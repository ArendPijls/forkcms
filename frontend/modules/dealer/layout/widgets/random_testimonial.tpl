{*
	variables that are available:
	- {$widgetTestimonialsRandomTestimonial}: contains data about a (random) testimonials
*}

<div class="randomTestimonial">
	{option:widgetTestimonialsRandomTestimonial}
		<div class="mod testimonial">
			<div class="inner">
				<blockquote class="bd">{$widgetTestimonialsRandomTestimonial.testimonial}</blockquote>
				<p class="name">{$widgetTestimonialsRandomTestimonial.name}</p>
			</div>
		</div>
	{/option:widgetTestimonialsRandomTestimonial}
</div>
