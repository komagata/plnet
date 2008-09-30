module ActiveRecord::ConnectionAdapters::SchemaStatements
  def add_foreign_key(from_table, from_column, to_table)
    constraint_name = "fk_#{from_table}_#{to_table}"
    execute "alter table #{from_table} add constraint \
      #{constraint_name} foreign key (#{from_column}) references #{to_table}(id)"
  end

  def set_auto_increment(table_name, number)
    execute "ALTER TABLE #{quote_table_name(table_name)} AUTO_INCREMENT=#{number}"
  end

  def load_fixture(fixture, dir = "test/fixtures")
    require "active_record/fixtures"
    Fixtures.create_fixtures(dir, fixture)
  end
end

ActiveSupport::CoreExtensions::Date::Conversions::DATE_FORMATS.merge!(
  :short_jp => "%m月%d日",
  :long_jp => "%Y年%m月%d日"
)
ActiveSupport::CoreExtensions::Time::Conversions::DATE_FORMATS.merge!(
  :short_jp => "%m月%d日 %H時%M分",
  :long_jp => "%Y年%m月%d日 %H時%M分"
)

module ApplicationHelper
  def web_root
    request.protocol+request.host_with_port
  end

  def free_dial?(str)
    /^0120/ =~ str ? true : false
  end

  def notice
    content_tag(:div, image_tag("notice.gif", :class => "icon")+flash[:notice], :id => "notice") if flash[:notice]
  end

  def warn
    content_tag(:div, image_tag("warn.gif", :class => "icon")+flash[:warn], :id => "warn") if flash[:warn]
  end

  def focus(element)
    content_tag(:script, "$('#{element}').focus()", :type => "text/javascript")
  end

  def will_paginate_ja(items)
    will_paginate items, :prev_label => "&#171;前", :next_label => "次&#187;"
  end

  def li_to_if_current_tab(controller_path, content = nil, options = {}, escape = true, &block)
    options[:class] = "selected" if controller.controller_path == controller_path
    content_tag(:li, content, options)
  end

  def options_by_prefectures
     Prefecture.all.collect {|p| [p.name, p.id] }.unshift ["-- 選択してください --", nil]
  end

  def prefecture_select(object, method)
    select object, method, options_by_prefectures
  end

  def header_title(str)
    content_tag :title, str
  end

  def meta_description(str)
    "<meta name=\"description\" content=\"#{str}\" />"
  end

  def meta_keywords(str)
    "<meta name=\"keywords\" content=\"#{str}\" />"
  end
end
