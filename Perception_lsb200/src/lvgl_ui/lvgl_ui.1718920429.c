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

static lv_style_t stytle_title;//�����µ���ʽ

extern lv_font_t myfont12;//�����ļ�
extern const lv_img_dsc_t img_bg;

/*���ݼ��ҳ��*/
lv_obj_t * chart;      //���ն���ʾͼ��ؼ�
lv_chart_series_t * s1;//ͼ��ؼ� ����
lv_obj_t * label1;     //���ն��ı���ʾ

lv_obj_t * label2;     //����״̬��ʾ
lv_obj_t * btn1;       //������ʾ��ť

lv_obj_t * label3;     //�¶��ı���ʾ
lv_obj_t * label4;     //ʪ���ı���ʾ
lv_obj_t * gauge2;     //��ʪ��������ʾ�Ǳ�


lv_obj_t * label5;     //��ѹ�ı���ʾ
lv_obj_t * gauge3;     //��ѹ������ʾ�Ǳ�


void lv_ex_gauge_1(void)
{
    lv_style_init(&stytle_title);//��ʽ��ʼ��
    lv_style_set_text_font(&stytle_title,LV_STATE_DEFAULT,&myfont12);//������ʽ����

    //����������
    lv_obj_t *parent=lv_scr_act();
    lv_obj_set_size(parent, 480, 800);
    lv_obj_set_style_local_bg_color(parent, LV_OBJ_PART_MAIN, LV_STATE_DEFAULT, LV_COLOR_BLACK);//

    //����ͼƬ
    lv_obj_t * img1 = lv_img_create(parent, NULL);
    lv_img_set_src(img1, &img_bg);
    lv_obj_align(img1, NULL, LV_ALIGN_CENTER, 0, 0);

    lv_obj_t *tabview;//����tabview����
    tabview = lv_tabview_create(parent, NULL);//����tabview�ؼ�
    lv_obj_set_style_local_bg_opa(tabview,LV_TABVIEW_PART_BG,LV_STATE_DEFAULT,LV_OPA_TRANSP);
    lv_tabview_set_anim_time(tabview,0);//���ÿؼ�����ʱ��Ϊ0���رն���

    lv_style_list_t * list = lv_obj_get_style_list(tabview, LV_LABEL_PART_MAIN);//��õ�ǰ�ؼ�����ʽ
    _lv_style_list_add_style(list, &stytle_title);//����µ���ʽ����ʽ
    lv_obj_refresh_style(tabview, LV_STYLE_PROP_ALL);//������ʽ

    lv_obj_t *tab1 = lv_tabview_add_tab(tabview, """\xE6\x95\xB0"/*��*/"""\xE6\x8D\xAE"/*��*/"""\xE7\x9B\x91"/*��*/"""\xE6\x8E\xA7"/*��*/"");

    visuals_create(tab1);//����ҳ��

}

static void visuals_create(lv_obj_t * parent)
{

    chart = lv_chart_create(parent, NULL);//����ͼ��ؼ�

    lv_obj_set_width_margin(chart, 450);//���ÿؼ����
    lv_obj_set_height_margin(chart, 280);//���ÿؼ��߶�
    lv_chart_set_div_line_count(chart, 3, 0);//����ͼ��ָ���������ֻ��ˮƽ�ָ���
    lv_chart_set_point_count(chart, 8);//����ͼ�����
    lv_chart_set_type(chart, LV_CHART_TYPE_LINE);//����Ϊ����ͼ
    lv_chart_set_range(chart, 0, 500);//����ͼ�����ݷ�Χ

    lv_obj_align(chart, parent, LV_ALIGN_IN_TOP_MID, 0, 10);//����ͼ��λ��


    //����ͼ����ʽ
    lv_obj_set_style_local_bg_opa(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, LV_OPA_80);
    lv_obj_set_style_local_bg_grad_dir(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, LV_GRAD_DIR_VER);
    lv_obj_set_style_local_bg_main_stop(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, 255);
    lv_obj_set_style_local_bg_grad_stop(chart, LV_CHART_PART_SERIES, LV_STATE_DEFAULT, 0);

    //����ͼ���ڿ�λ��
    lv_obj_set_style_local_pad_left(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 5 * (LV_DPI / 13));
    lv_obj_set_style_local_pad_bottom(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 3 * (LV_DPI / 13));
    lv_obj_set_style_local_pad_right(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 2 * (LV_DPI / 13));
    lv_obj_set_style_local_pad_top(chart,  LV_CHART_PART_BG, LV_STATE_DEFAULT, 2 * (LV_DPI / 13));

    //����ͼ��������ָʾ���������
    lv_chart_set_y_tick_length(chart, 0, 0);//����Y��̶��߳���Ϊ0
    lv_chart_set_x_tick_length(chart, 0, 0);//����X��̶��߳���Ϊ0
    lv_chart_set_y_tick_texts(chart, "500\n400\n300\n200\n100\n0", 0, LV_CHART_AXIS_DRAW_LAST_TICK);
    lv_chart_set_x_tick_texts(chart, "1\n2\n3\n4\n5\n6\n7\n8", 0, LV_CHART_AXIS_DRAW_LAST_TICK);
    //�����
    s1 = lv_chart_add_series(chart, LV_THEME_DEFAULT_COLOR_PRIMARY);

    //��������������ʾ�ı�
    label1 = lv_label_create(parent, NULL);
    lv_label_set_long_mode(label1, LV_LABEL_LONG_BREAK);    //���ó��ı�ģʽ
    lv_label_set_recolor(label1, true);                  //�����ı���ɫ�л�
    lv_label_set_align(label1, LV_LABEL_ALIGN_CENTER);    //�����ı�����
    lv_obj_set_width(label1, 300);//�����ı�����

    //�޸��ı��ؼ���ʽ����������
    lv_style_list_t * list = lv_obj_get_style_list(label1, LV_LABEL_PART_MAIN);
    _lv_style_list_add_style(list, &stytle_title);
    lv_obj_refresh_style(label1,LV_STYLE_PROP_ALL);

    lv_obj_align(label1, chart, LV_ALIGN_OUT_BOTTOM_MID, 0, 10);//�����ı�λ��


    static lv_color_t needle_colors[3]; //����ָ����ɫ����
    needle_colors[0] = LV_COLOR_BLUE;   //����ָ����ɫΪ��ɫ
    needle_colors[1] = LV_COLOR_YELLOW;    //����ָ����ɫΪ��ɫ


    btn1 = lv_btn_create(parent, NULL);
    lv_obj_align(btn1, label1, LV_ALIGN_OUT_BOTTOM_MID, -67, 40);
    lv_obj_set_size(btn1,250,60);

    label2 = lv_label_create(parent, NULL);     //��������״̬��ʾ�ı��ؼ�
    lv_label_set_recolor(label2, true);         //����������ɫ���޸�����
    lv_obj_set_width(label2,250);
    lv_obj_align(label2, btn1, LV_ALIGN_CENTER, -20, 0);//����λ��


    gauge2 = lv_gauge_create(parent, NULL);//�����Ǳ��̿ؼ�
    lv_gauge_set_needle_count(gauge2, 2, needle_colors);//�����Ǳ���Ϊ2��ָ��
    lv_obj_set_size(gauge2, 200, 200);//���ô�С
    lv_gauge_set_range(gauge2, 0, 100);//����������ʾ��Χ
    lv_gauge_set_critical_value(gauge2,80);//�����Ǳ����ٽ�ֵ
    lv_obj_align(gauge2, btn1, LV_ALIGN_OUT_BOTTOM_LEFT, -70, 50);//����λ��

    label3 = lv_label_create(parent, NULL);//�����¶���ʾ�ı��ؼ�
    lv_label_set_recolor(label3, true);//����������ɫ���޸�����
    lv_obj_align(label3, gauge2, LV_ALIGN_IN_BOTTOM_MID, -20, -30);//����λ��

    label4 = lv_label_create(parent, NULL);//����ʪ����ʾ�ı��ؼ�
    lv_label_set_recolor(label4, true);//����������ɫ���޸�����
    lv_obj_align(label4, gauge2, LV_ALIGN_IN_BOTTOM_MID, -20, -15);//����λ��


    gauge3 = lv_gauge_create(parent, NULL);//�����Ǳ��̿ؼ�
    lv_gauge_set_needle_count(gauge3, 1, needle_colors);//�����Ǳ���Ϊһ��ָ��
    lv_obj_set_size(gauge3, 200, 200);//���ô�С
    lv_gauge_set_range(gauge3, 0, 200);//����������ʾ��Χ
    lv_gauge_set_critical_value(gauge3,180);//�����Ǳ����ٽ�ֵ
    lv_obj_align(gauge3, gauge2, LV_ALIGN_OUT_RIGHT_MID, 10, 0);//����λ��

    label5 = lv_label_create(parent, NULL);//������ѹ��ʾ�ı��ؼ�
    lv_label_set_recolor(label5, true);//����������ɫ���޸�����
    lv_obj_align(label5, gauge3, LV_ALIGN_IN_BOTTOM_MID, -25, -25);//����λ��

    //��������
    lv_task_create(my_task1, 500, LV_TASK_PRIO_LOW, NULL);//�������ݶ�ȡ����
    //��������
    lv_task_create(my_task2, 500, LV_TASK_PRIO_LOW, NULL);//����������ʾ����
    
}


//���ݽ���
unsigned char senaor_get_flag=0;
void my_task1(lv_task_t * task)
{
    switch(senaor_get_flag)
    {
        case 0:
            sensor_data_get(0);//��ȡ����
            break;
        case 1:
            sensor_data_get(1);//��ȡ����
            break;
        case 2:
            sensor_data_get(2);//��ȡ����
            break;
        case 3:
            sensor_data_get(3);//��ȡ����
            break;
    }
    senaor_get_flag++;
    if(senaor_get_flag>=3)
    {
        senaor_get_flag=0;
    }
}


//������ʾ����
void my_task2(lv_task_t * task)
{
    unsigned char buf[50] = {0};

    lv_chart_set_next(chart, s1, light);//��ӹ������ݵ�ͼ��
    sprintf((char *)buf," #ffffff Light:%.02f lx # ", light);
    lv_label_set_text(label1, buf);//��ʾ�������ݵ��ı��ؼ�

    lv_gauge_set_value(gauge2, 0, temp);//�����Ǳ��̿ؼ�����
    lv_gauge_set_value(gauge2, 1, hum); //�����Ǳ��̿ؼ�����
    sprintf((char *)buf,"%.1f \xE2\x84\x83", temp);
    lv_label_set_text_fmt(label3," #01a2b1 %.1f 'C # ",temp);//�����ı��ؼ���ʾ�¶�����
    sprintf((char *)buf,"%.1f %%rh", hum);
    lv_label_set_text_fmt(label4," #01a2b1 %.1f %%rh # ",hum);//�����ı��ؼ���ʾ�¶�����

    lv_gauge_set_value(gauge3, 0, press);//�����Ǳ��̿ؼ�����
    sprintf((char *)buf,"%.02f kpa", press);
    lv_label_set_text_fmt(label5," #01a2b1 %.02f kpa # ",press);//�����ı��ؼ���ʾ��ѹ����

    if(sta == 0)
    {
        //��ʾ��������ɫ��
        lv_label_set_text(label2," #00ff00 ""\xE6\xA3\x80"/*��*/"""\xE6\xB5\x8B"/*��*/"""\xE6\xAD\xA3"/*��*/"""\xE5\xB8\xB8"/*��*/"#");
    }
    else
    {
        lv_obj_align(label2, btn1, LV_ALIGN_CENTER, 0, 0);//����λ��
        //��ʾ��鵽��ȼ���壨��ɫ��
        lv_label_set_text(label2," #ff0000 ""\xE6\xB3\xA8"/*ע*/"""\xE6\x84\x8F"/*��*/"""\xEF\xBC\x81"/*��*/"""\xE6\xA3\x80"/*��*/"""\xE6\xB5\x8B"/*��*/"""\xE5\x88\xB0"/*��*/"""\xE5\x8F\xAF"/*��*/"""\xE7\x87\x83"/*ȼ*/"""\xE6\xB0\x94"/*��*/"""\xE4\xBD\x93"/*��*/" # ");
    }
    
}





