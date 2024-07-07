/*
 * lvgl_ui.c
 *
 * created: 2024/1/20
 *  author:
 */

#include "lvgl-7.0.1/lvgl.h"
#include "lvgl_ui.h"

#include <stdio.h>
#include <stdlib.h>
#include "ls1b.h"
#include "mips.h"

#include "bsp.h"
#include "ls1x_fb.h"
#include "ls1b_gpio.h"
#include "buzzer.h"
#include "relay.h"

#include "ls1x_rtc.h"
#include "ls1x_can.h"

static lv_style_t stytle_title;//创建新的样式

extern lv_font_t myfont12;//字体文件
extern const lv_img_dsc_t img_bg;

/*数据监控页面*/
lv_obj_t * chart;      //光照度显示图表控件
lv_chart_series_t * s1;//图表控件 线条
lv_obj_t * label1;     //光照度文本显示

lv_obj_t * label2;     //烟雾状态显示
lv_obj_t * btn1;       //烟雾显示按钮

lv_obj_t * label3;     //温度文本显示
lv_obj_t * label4;     //湿度文本显示
lv_obj_t * gauge2;     //温湿度数据显示仪表


lv_obj_t * label5;     //气压文本显示
lv_obj_t * gauge3;     //气压数据显示仪表


void lv_ex_gauge_1(void)
{
    lv_style_init(&stytle_title);//样式初始化
    lv_style_set_text_font(&stytle_title,LV_STATE_DEFAULT,&myfont12);//设置样式字体

    //创建主布局
    lv_obj_t *parent=lv_scr_act();
    lv_obj_set_size(parent, 480, 800);
    lv_obj_set_style_local_bg_color(parent, LV_OBJ_PART_MAIN, LV_STATE_DEFAULT, LV_COLOR_BLACK);//

    //背景图片
    lv_obj_t * img1 = lv_img_create(parent, NULL);
    lv_img_set_src(img1, &img_bg);
    lv_obj_align(img1, NULL, LV_ALIGN_CENTER, 0, 0);

    lv_obj_t *tabview;//创建tabview对象
    tabview = lv_tabview_create(parent, NULL);//创建tabview控件
    lv_obj_set_style_local_bg_opa(tabview,LV_TABVIEW_PART_BG,LV_STATE_DEFAULT,LV_OPA_TRANSP);
    lv_tabview_set_anim_time(tabview,0);//设置控件动画时间为0，关闭动画

    lv_style_list_t * list = lv_obj_get_style_list(tabview, LV_LABEL_PART_MAIN);//获得当前控件的样式
    _lv_style_list_add_style(list, &stytle_title);//添加新的样式到样式
    lv_obj_refresh_style(tabview, LV_STYLE_PROP_ALL);//更新样式

    lv_obj_t *tab1 = lv_tabview_add_tab(tabview, """\xE6\x95\xB0"/*数*/"""\xE6\x8D\xAE"/*据*/"""\xE7\x9B\x91"/*监*/"""\xE6\x8E\xA7"/*控*/"");

    visuals_create(tab1);//创建页面

}

static void visuals_create(lv_obj_t * parent)
{

    chart = lv_chart_create(parent, NULL);//创建图表控件

    lv_obj_set_width_margin(chart, 450);//设置控件宽度
    lv_obj_set_height_margin(chart, 280);//设置控件高度
    lv_chart_set_div_line_count(chart, 3, 0);//设置图表分割线数量，只有水平分割线
    lv_chart_set_point_count(chart, 8);//设置图表点数
    lv_chart_set_type(chart, LV_CHART_TYPE_LINE);//设置为折线图
    lv_chart_set_range(chart, 0, 500);//设置图表数据范围

    lv_obj_align(chart, parent, LV_ALIGN_IN_TOP_MID, 0, 10);//设置图表位置


    //设置图表样式
    lv_obj_set_style_local_bg_opa(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, LV_OPA_80);
    lv_obj_set_style_local_bg_grad_dir(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, LV_GRAD_DIR_VER);
    lv_obj_set_style_local_bg_main_stop(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, 255);
    lv_obj_set_style_local_bg_grad_stop(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, 0);

    //设置图表内框位置
    lv_obj_set_style_local_pad_left(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 5 * (LV_DPI / 13));
    lv_obj_set_style_local_pad_bottom(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 3 * (LV_DPI / 13));
    lv_obj_set_style_local_pad_right(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 2 * (LV_DPI / 13));
    lv_obj_set_style_local_pad_top(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 2 * (LV_DPI / 13));

    //设置图表外数据指示风格与数据
    lv_chart_set_y_tick_length(chart, 0, 0);//设置Y轴刻度线长度为0
    lv_chart_set_x_tick_length(chart, 0, 0);//设置X轴刻度线长度为0
    lv_chart_set_y_tick_texts(chart, "500\n400\n300\n200\n100\n0", 0, LV_CHART_AXIS_DRAW_LAST_TICK);
    lv_chart_set_x_tick_texts(chart, "1\n2\n3\n4\n5\n6\n7\n8", 0, LV_CHART_AXIS_DRAW_LAST_TICK);
    //添加线
    s1 = lv_chart_add_series(chart, LV_THEME_DEFAULT_COLOR_PRIMARY);

    //创建光照数据显示文本
    label1 = lv_label_create(parent, NULL);
    lv_label_set_long_mode(label1, LV_LABEL_LONG_BREAK);    //设置长文本模式
    lv_label_set_recolor(label1, true);                  //开启文本颜色切换
    lv_label_set_align(label1, LV_LABEL_ALIGN_CENTER);    //设置文本居中
    lv_obj_set_width(label1, 300);//设置文本长度

    //修改文本控件样式，更换字体
    lv_style_list_t * list = lv_obj_get_style_list(label1, LV_LABEL_PART_MAIN);
    _lv_style_list_add_style(list, &stytle_title);
    lv_obj_refresh_style(label1,LV_STYLE_PROP_ALL);

    lv_obj_align(label1, chart, LV_ALIGN_OUT_BOTTOM_MID, 0, 10);//设置文本位置


    static lv_color_t needle_colors[3]; //创建指针颜色数组
    needle_colors[0] = LV_COLOR_BLUE;   //设置指针颜色为蓝色
    needle_colors[1] = LV_COLOR_YELLOW;    //设置指针颜色为黄色


    btn1 = lv_btn_create(parent, NULL);
    lv_obj_align(btn1, label1, LV_ALIGN_OUT_BOTTOM_MID, -67, 40);
    lv_obj_set_size(btn1,250,60);

    label2 = lv_label_create(parent, NULL);     //创建烟雾状态显示文本控件
    lv_label_set_recolor(label2, true);         //设置字体颜色可修改属性
    lv_obj_set_width(label2,250);
    lv_obj_align(label2, btn1, LV_ALIGN_CENTER, -20, 0);//设置位置


    gauge2 = lv_gauge_create(parent, NULL);//创建仪表盘控件
    lv_gauge_set_needle_count(gauge2, 2, needle_colors);//设置仪表盘为2个指针
    lv_obj_set_size(gauge2, 200, 200);//设置大小
    lv_gauge_set_range(gauge2, 0, 100);//设置数据显示范围
    lv_gauge_set_critical_value(gauge2,80);//设置仪表盘临界值
    lv_obj_align(gauge2, btn1, LV_ALIGN_OUT_BOTTOM_LEFT, -70, 50);//设置位置

    label3 = lv_label_create(parent, NULL);//创建温度显示文本控件
    lv_label_set_recolor(label3, true);//设置字体颜色可修改属性
    lv_obj_align(label3, gauge2, LV_ALIGN_IN_BOTTOM_MID, -20, -30);//设置位置

    label4 = lv_label_create(parent, NULL);//创建湿度显示文本控件
    lv_label_set_recolor(label4, true);//设置字体颜色可修改属性
    lv_obj_align(label4, gauge2, LV_ALIGN_IN_BOTTOM_MID, -20, -15);//设置位置


    gauge3 = lv_gauge_create(parent, NULL);//创建仪表盘控件
    lv_gauge_set_needle_count(gauge3, 1, needle_colors);//设置仪表盘为一个指针
    lv_obj_set_size(gauge3, 200, 200);//设置大小
    lv_gauge_set_range(gauge3, 0, 200);//设置数据显示范围
    lv_gauge_set_critical_value(gauge3,180);//设置仪表盘临界值
    lv_obj_align(gauge3, gauge2, LV_ALIGN_OUT_RIGHT_MID, 10, 0);//设置位置

    label5 = lv_label_create(parent, NULL);//创建气压显示文本控件
    lv_label_set_recolor(label5, true);//设置字体颜色可修改属性
    lv_obj_align(label5, gauge3, LV_ALIGN_IN_BOTTOM_MID, -25, -25);//设置位置

    //创建任务
    lv_task_create(my_task1, 500, LV_TASK_PRIO_LOW, NULL);//创建数据读取任务
    //创建任务
    lv_task_create(my_task2, 500, LV_TASK_PRIO_LOW, NULL);//创建数据显示任务
    
}


//数据接收
unsigned char senaor_get_flag=0;
void my_task1(lv_task_t * task)
{
    switch(senaor_get_flag)
    {
        case 0:
            sensor_data_get(0);//获取数据
            break;
        case 1:
            sensor_data_get(1);//获取数据
            break;
        case 2:
            sensor_data_get(2);//获取数据
            break;
        case 3:
            sensor_data_get(3);//获取数据
            break;
    }
    senaor_get_flag++;
    if(senaor_get_flag>=3)
    {
        senaor_get_flag=0;
    }
}


//数据显示任务
void my_task2(lv_task_t * task)
{
    unsigned char buf[50] = {0};

    lv_chart_set_next(chart, s1, light);//添加光照数据到图表
    sprintf((char *)buf," #ffffff Light:%.02f lx # ", light);
    lv_label_set_text(label1, buf);//显示光照数据到文本控件

    lv_gauge_set_value(gauge2, 0, temp);//设置仪表盘控件数据
    lv_gauge_set_value(gauge2, 1, hum); //设置仪表盘控件数据
    sprintf((char *)buf,"%.1f \xE2\x84\x83", temp);
    lv_label_set_text_fmt(label3," #01a2b1 %.1f 'C # ",temp);//设置文本控件显示温度数据
    sprintf((char *)buf,"%.1f %%rh", hum);
    lv_label_set_text_fmt(label4," #01a2b1 %.1f %%rh # ",hum);//设置文本控件显示温度数据

    lv_gauge_set_value(gauge3, 0, press);//设置仪表盘控件数据
    sprintf((char *)buf,"%.02f kpa", press);
    lv_label_set_text_fmt(label5," #01a2b1 %.02f kpa # ",press);//设置文本控件显示气压数据

    if(sta == 0)
    {
        //显示正常（绿色）
        lv_label_set_text(label2," #00ff00 ""\xE6\xA3\x80"/*检*/"""\xE6\xB5\x8B"/*测*/"""\xE6\xAD\xA3"/*正*/"""\xE5\xB8\xB8"/*常*/"#");
    }
    else
    {
        lv_obj_align(label2, btn1, LV_ALIGN_CENTER, 0, 0);//设置位置
        //显示检查到可燃气体（红色）
        lv_label_set_text(label2," #ff0000 ""\xE6\xB3\xA8"/*注*/"""\xE6\x84\x8F"/*意*/"""\xEF\xBC\x81"/*！*/"""\xE6\xA3\x80"/*检*/"""\xE6\xB5\x8B"/*测*/"""\xE5\x88\xB0"/*到*/"""\xE5\x8F\xAF"/*可*/"""\xE7\x87\x83"/*燃*/"""\xE6\xB0\x94"/*气*/"""\xE4\xBD\x93"/*体*/" # ");
    }
    
}





