/*
 * sensor_lvgl_ui.h
 *
 * created: 2021/10/21
 *  author:
 */

#ifndef _LVGL_UI_H
#define _LVGL_UI_H

#include "lvgl-7.0.1/lvgl.h"

extern unsigned int sta;   //ÑÌÎí×´Ì¬
extern float temp;
extern float hum;
extern float press;
extern float eleva;
extern float light;


void my_task1(lv_task_t * task);
void my_task2(lv_task_t * task);
static void visuals_create(lv_obj_t * parent);

void lv_ex_btnmatrix_1(void);

#endif // _SENSOR_LVGL_UI_H



