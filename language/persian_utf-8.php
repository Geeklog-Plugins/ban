<?php

###############################################################################
# persian_utf-8.php
#
# This is the Persian language file for Geeklog ban plugin
# Special thanks to Mahdi Montazeri for his work on this project
#
# Copyright (C) 2018 geeklog.ir
# info AT mahdimontazeri DOT com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

## $Id:

###############################################################################
# Array Format:
# $LANGXX[YY]:  $LANG - variable name
#               XX    - file id number
#               YY    - phrase id number
###############################################################################

/**
* General language
*/

$LANG_BAN00 = array (
    'ban'           => 'ممنوعیت',
    'type'          => 'نوع',
    'data'          => 'داده',
    'id'            => 'شناسه',
    'status'        => 'وضعیت',
    'created'       => 'ایجاد شده',
    'ttl'           => 'زمان زیست',
    'note'          => 'توجه',
    'desc'          => 'ممنوعیت آیپی ها و ربات های بد از سایت اینترنتی خود.',
    'instructions'  => 'می توانید ربات ها و افراد را از سایت اینترنتی خود ممنوع کنید.  شما می توانید ممنوع کنید توسط آیپی دقیق (use REMOTE_ADDR), رجوعی دقیق (use HTTP_REFERER), عامل کاربر دقیق (use HTTP_USER_AGENT) یا صفحه دقیق (use SCRIPT_NAME). برای تطابق الگو هایی که استفاده می کنند از عبارات عادی غیر حساس (preg_match). با REMOTE_ADDR_RANGE همچنین می توانید محدوده های نشانی آیپی را اضافه کنید و با REMOTE_ADDR_CIDR از نشان گذاری CIDR استفاده کنید یا به صورت ساده محدوده های from-to. افزونه ممنوعیت را ببینید <a href="' . $_CONF['site_admin_url'] . '/plugins/ban/readme.html" target="_blank">Read Me</a> برای اطلاعات بیشتر.<br' . XHTML . '><br' . XHTML . '>اگر از ممنوعیت خودکار با افزونه آمار کاربردی استفاده می کنید لطفا مطمئن شوید ترتیب بارکنش افزونه تنظیم شده است بنابراین افزونه آمار کاربردی قبل از افزونه ممنوعیت بارکنش شده است.',
    'instructions_sfs'              => '<br' . XHTML . '><br' . XHTML . '>آخرین باری که پایگاه داده توقف هرزنامه انجمن شما بروزرسانی شد، بود در: %s',
    'instructions_sfs_size'         => '<br' . XHTML . '><br' . XHTML . '>اندازه پایگاه داده توقف هرزنامه انجمن فعلی شما %s کیلوبایت و معتبر به نظر می رسد. معمولا اندازه پرونده بالای ۲۰۰۰ کیلوبایت است.',
    'instructions_sfs_size_small'   => '<br' . XHTML . '><br' . XHTML . '><em>هشدار:</em> اندازه پایگاه داده توقف هرزنامه انجمن فعلی شما %s کیلوبایت است. معمولا اندازه پرونده بالای ۲۰۰۰ کیلوبایت است. شما باید تأیید کنید که پایگاه داده توقف هرزنامه انجمن معتبر است.',
    'instructions_sfs_size_error'   => '<br' . XHTML . '><br' . XHTML . '><em>هشدار:</em> اندازه پایگاه داده توقف هرزنامه انجمن فعلی شما قابل تشخیص نمی باشد. پرونده ممکن است خراب باشد. توصیه می شود پایگاه داده توقف هرزنامه انجمن را مجدد بارگیری کنید یا این ویژگی را غیر فعال کنید زیرا آیپی های معتبر ممکن است به صورت تصادفی ممنوع شوند.',
    'instructions_sfs_db_missing'   => '<br' . XHTML . '><br' . XHTML . '><em>هشدار:</em> توقف هرزنامه انجمن فعال است اما پرونده پایگاه داده %s در سرور شما یافت نمی شود. لطفا پرونده را بارگیری کنید یا گزینه پیکربندی ممنوعیت توقف هرزنامه انجمن را غیرفعال کنید.',
    'instructions_info' => '<br' . XHTML . '><br' . XHTML . '>برای ارجاع اطلاعات مرورگر شما هست:<br' . XHTML . '><br' . XHTML . '>HTTP_USER_AGENT: %s<br' . XHTML . '><br' . XHTML . '>REMOTE_ADDR: %s<br' . XHTML . '><br' . XHTML . '>HTTP_REFERER: %s<br' . XHTML . '><br' . XHTML . '>SCRIPT_NAME: %s<br' . XHTML . '><br' . XHTML . '>',
    'not_available' => 'موجود نمی باشد',
    'save'          => 'ذخیره',
    'cancel'        => 'لغو',
    'delete'        => 'حذف',
    'edit'          => 'ویرایش',
    'ban_editor'    => 'ویرایشگر ممنوعیت',
    'ban_list'      => 'فهرست ممنوعیت',
    'log_viewer'    => 'نمایشگر لاگ',
    'download_sfs'  => 'بارگیری پایگاه داده توقف هرزنامه انجمن',

    'status_normal'         => 'عادی',
    'status_ttl_short'      => 'زمان زیست کوتاه',
    'status_ttl_medium'     => 'زمان زیست متوسط',
    'status_ttl_long'       => 'زمان زیست طولانی',
    'status_white'          => 'سفید',
    
    'ban_plugin_note'		=> 'ممنوع شده توسط افزونه %s.', 
    
    'ban_invalid_logins_note'		=> 'حداکثر تعداد ورود های نامعتبر به سر رسیده برای شناسه کاربر: %s', // specify id since user cannot change that
    
    'gus_user_agent_note'   => "عاملان کاربر آمار کاربردی فراتر رفت ({$_BAN_CONF['gus_user_agent_num']}) برای آیپی در آخرین {$_BAN_CONF['gus_user_agent_time']} ثانیه.",
    'gus_hits_note'         => "بازدید آمار کاربردی فراتر رفت ({$_BAN_CONF['gus_hits_num']}) برای آیپی در آخرین {$_BAN_CONF['gus_hits_time']} ثانیه.",
    'gus_referrer_note'     => "رجوعی آمار کاربردی فراتر رفت ({$_BAN_CONF['gus_referrer_num']}) تطابقات برای آیپی در آخرین {$_BAN_CONF['gus_referrer_time']} ثانیه.",
    'gus_url_note'          => "صفحه و درخواست های رشته جستار آمار کاربردی فراتر رفت ({$_BAN_CONF['gus_url_num']}) تطابقات برای آیپی در آخرین {$_BAN_CONF['gus_url_time']} ثانیه.",
    
    'stopforumspam_note'   => "پایگاه داده توقف هرزنامه انجمن شما قدیمی تر است از {$_BAN_CONF['stopforumspam_file_date']} روز. لطفا یک پایگاه داده جدید را بارگیری کنید در <a href='http://www.stopforumspam.com/downloads/' target='_blank'>سایت اینترنتی توقف هرزنامه انجمن</a>. اکنون برای بارگیری خودکار آن، <a href='/admin/plugins/ban/index.php?mode=sfs_download'>اینجا کلیک کنید</a>. به خاطر  داشته باشید برای انجام بارگیری خودکار لازم است مطمئن شوید که سایت اینترنتی شما دسترسی نوشتن برای پرونده پایگاه داده توقف هرزنامه انجمن را دارد.",
    
    'error_editor_no_data'  => 'ممنوعیت شما باید دارای یک نوع، وضعیت و داده باشد. ترکیب نوع و داده نیز در پایگاه داده نباید از قبل وجود داشته باشد.',
    'access_denied'         => 'دسترسی ممنوع است',
	'access_denied_msg'     => 'فقط کاربران ریشه یا کاربران ممنوعه دسترسی به این صفحه دارند. نام کاربری و آیپی شما ضبط شده است.'
);

###############################################################################
# Messages for COM_showMessage the submission form

$PLG_ban_MESSAGE1 = "ممنوعیت شما با موفقیت ذخیره شده است.";
$PLG_ban_MESSAGE2 = 'ممنوعیت شما با موفقیت حذف شده است.';
$PLG_ban_MESSAGE3 = 'ممنوعیت های منتخب شما با موفقیت حذف شده اند.';
$PLG_ban_MESSAGE4 = 'پایگاه داده توقف هرزنامه انجمن شما متوقف شده بروزرسانی شده است.';
$PLG_ban_MESSAGE5 = 'مشکلی در بارگیری یا ناهمفشردن پایگاه داده توقف هرزنامه انجمن وجود داشت. بسته به تنظیمات شما هر ۸ ساعت فقط حداقل ۱ تلاش برای بارگیری دارید. برای اطلاعات بیشتر لطفا پرونده لاگ خطای سایت خود را بررسی کنید.';
