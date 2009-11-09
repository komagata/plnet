#!/usr/bin/env ruby

$KCODE="U"

require 'rubygems'
require 'rack'
require 'mechanize'
require 'kconv'
require 'cgi'
require 'rss'
require 'logger'

include Rack

def u8 str
  CGI.unescapeHTML(str.toutf8)
end

def to_time str
  Time.parse(u8(str.toutf8).gsub(/年|月/, '-').gsub(/<br \/>/, '-').gsub(/日/, ''))
end

mixi_diary_app = Proc.new do |env|
  Response.new.finish do |res|
    rss_base_url     = "http://plnet.jp/"
    mixi_url         = "http://mixi.jp/"
    friend_path      = "show_friend.pl?id="
    diary_list_path  = "list_diary.pl?id="
    fetch_diary_size = 2
    username         = "komagata@p0t.jp"
    password         = "komagata"

    user_id = Request.new(env).params["user_id"]
    agent    = WWW::Mechanize.new
    diary    = {
      "author" => "",
      "title" => "",
      "entries" => []
    }

    logger = Logger.new("mixi_diary.log")
    logger.level = Logger::DEBUG

    logger.debug("start")

    # login
    login_page             = agent.get(mixi_url)
    login_form             = login_page.forms.first
    login_form['email']    = username
    login_form['password'] = password

    agent.submit(login_form)

    logger.debug "login succeed"

    # diary list
    diary_urls = []
    diary_list_page = agent.get("#{diary_list_path}#{user_id}")
    logger.debug "dialy_list_url: #{diary_list_path}#{user_id}"

    name_el = diary_list_page.root.search(
      "table[@width='525']/tr/td[@width='490']/b/font"
    )
    diary["author"] = u8(name_el.text).gsub(/さんの日記/, '')
    diary["title"] = u8(name_el.text)

    diary_list_page.root.search(
      "table/tr/td/table/tr[@valign='top']/td[@bgcolor='#FFF4E0']/a"
    ).each do |a|
      diary_urls << "#{mixi_url}#{a.attributes['href']}"
    end

    logger.debug "diary size: #{diary_urls.size}"

    threads = []
    diary_urls = diary_urls[0, fetch_diary_size]
    diary_urls.each_with_index do |url, index|
      logger.debug "diary_url: #{url}"

      threads << Thread.new(agent.clone, url, index, logger) do |a, u, i, logger|

        begin

          logger.debug "thread start: number #{i}"

          page = a.get(u)

          logger.debug "get page"

          trs = page.root.search(
            "table/tr/td/table/tr/td/table/tr/td[@colspan='2']/table[@cellpadding='3']"
          ).first.search("tr")

          logger.debug "parse page"

          entry = {
            'url'   => "#{mixi_url}#{u}",
            'title' => u8((trs/"td[@bgcolor='#FFF4E0']").inner_html).gsub(/&nbsp;/, ''),
            'body'  => u8((trs/"td[@class='h12']").inner_html),
            'date'  => to_time((trs/"td[@rowspan='2']").inner_html)
          }

          logger.debug "entry: #{entry.inspect}"

        rescue => err
          logger.fatal("thread error, thread number: #{i}, url: #{u}")
          logger.fatal(err)
        end

        entry

      end
    end

    logger.debug "all threads booted"

    threads.each {|t| diary["entries"] << t.value }

    logger.debug "all threads return value"
    logger.debug "results diary size: #{diary.size}"

    # to rss
    rss = RSS::Maker.make("1.0") do |maker|
      maker.channel.about = "#{rss_base_url}mixi_diary/#{user_id}"
      maker.channel.title = diary["title"]
      maker.channel.description = "#{diary["author"]}さんのMixi日記"
      maker.channel.link = "#{mixi_url}#{friend_path}#{user_id}"

      diary["entries"].each do |key, entry|
        item             = maker.items.new_item
        item.link        = entry["url"]
        item.title       = entry["title"]
        item.date        = entry["date"]
        item.description = entry["body"]
        item.author      = diary["author"]
      end
    end

    res.write rss.to_s

    logger.debug("end")
  end
end

Handler::WEBrick.run ShowExceptions.new(mixi_diary_app), :Port => 3000
#Handler::FastCGI.run ShowExceptions.new(mixi_diary_app)
