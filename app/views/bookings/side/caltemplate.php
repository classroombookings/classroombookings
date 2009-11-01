{table_open}<table border="0" cellpadding="0" cellspacing="0" class="ccb" width="100%">{/table_open}

   {heading_row_start}<tr class="head">{/heading_row_start}

   
   {heading_previous_cell}<th><a href="{previous_url}" rel="calmonth"><img src="img/ico/arrow-black-circle-w.png" alt="&lt;&lt;" /></a></th>{/heading_previous_cell}
   {heading_previous_cell_empty}<th>&nbsp;</th>{/heading_previous_cell_empty}
   {heading_title_cell}<th colspan="{colspan}" class="heading">{heading}</th>{/heading_title_cell}
   {heading_next_cell}<th><a href="{next_url}" rel="calmonth"><img src="img/ico/arrow-black-circle-e.png" alt="&gt;&gt;" /></a></th>{/heading_next_cell}
   {heading_next_cell_empty}<th>&nbsp;</th>{/heading_next_cell_empty}

   {heading_row_end}</tr>{/heading_row_end}

   {week_row_start}<tr class="wr">{/week_row_start}
   {week_day_cell}<td>{week_day}</td>{/week_day_cell}
   {week_row_end}</tr>{/week_row_end}

   {cal_row_start}<tr{rowclass}>{/cal_row_start}
   {cal_cell_start}<td>{/cal_cell_start}

   {cal_cell_content}<a href="{content}" rel="date">{day}</a>{/cal_cell_content}
   {cal_cell_content_today}<a href="{content}" rel="date" class="current">{day}</a></div>{/cal_cell_content_today}

   {cal_cell_no_content}{day}{/cal_cell_no_content}
   {cal_cell_no_content_today}<div class="highlight">{day}</div>{/cal_cell_no_content_today}

   {cal_cell_blank}&nbsp;{/cal_cell_blank}

   {cal_cell_end}</td>{/cal_cell_end}
   {cal_row_end}</tr>{/cal_row_end}

   {table_close}</table>{/table_close}