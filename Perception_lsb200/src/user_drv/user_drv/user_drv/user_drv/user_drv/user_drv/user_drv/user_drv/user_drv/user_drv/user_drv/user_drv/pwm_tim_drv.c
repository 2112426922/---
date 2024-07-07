/*
 * rtc_drv.c
 *
 * created: 2021/11/1
 *  author:
 */

#include "ls1x_rtc.h"
#include "pwm_tim_drv.h"
#include "lvgl-7.0.1/lvgl.h"
#include "ls1x_pwm.h"
extern void *devPWM2;

//定义一个PWM的结构体
pwm_cfg_t pwm2_cfg;

//PWM2中断处理函数
static void pwmtimer_callback(void *pwm, int *stopit)
{
    lv_tick_inc(10);

}


void PWM2_Time_Init(void)
{
    pwm2_cfg.hi_ns = 16777216;
    pwm2_cfg.lo_ns = 0;
    pwm2_cfg.mode = PWM_CONTINUE_TIMER;//脉冲持续产生
    pwm2_cfg.cb = pwmtimer_callback;
    pwm2_cfg.isr = NULL; //工作在定时器模式

    ls1x_pwm_init(devPWM2,NULL);
    ls1x_pwm_open(devPWM2,(void *)&pwm2_cfg);
    ls1x_pwm_timer_start(devPWM2,(void *)&pwm2_cfg);
}

