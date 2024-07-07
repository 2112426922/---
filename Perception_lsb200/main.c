//---------------------------------------------------------------------------
// 必要的库头文件   8600
//---------------------------------------------------------------------------
#include <time.h>
#include <stdio.h>
#include "rtthread.h"
#include "ls1b.h"
#include "mips.h"

#include "ls1x_Watchdog.h"

#include <malloc.h>
#include <string.h>
#include <stdlib.h>
#include <stdbool.h>

//---------------------------------------------------------------------------
// BSP头文件
//---------------------------------------------------------------------------
#include "bsp.h"
#include "ls1x_fb.h"
#include "ls1b_gpio.h"
char LCD_display_mode[] = LCD_480x800;

//---------------------------------------------------------------------------
// lvgl头文件
//---------------------------------------------------------------------------
#include "lvgl_ui.h"
//---------------------------------------------------------------------------
// src头文件
//---------------------------------------------------------------------------
#include "other.h"
#include "hdc2080.h"
#include "tsl2561fn.h"
#include "SPL06_007.h"
#include "buzzer.h"

#include "ns16550.h"
#include "key.h"
#include "gpio_uart.h"
#include "relay.h"

//---------------------------------------------------------------------------
// 其他头文件
//---------------------------------------------------------------------------
#include "ls1x_can.h"
#include "ls1x_rtc.h"
#include "rs485_drv.h"
#include "ls1x_i2c_bus.h"

//---------------------------------------------------------------------------
// 全局变量
//---------------------------------------------------------------------------
unsigned int sta = 0;
float temp = 0;
float hum = 0;
float press = 0;
float eleva = 0;
float light = 0;

//设备编号
#define DEVICE_NUMBER 240310024
unsigned int code = DEVICE_NUMBER;

char buff[10];
char buf[5]="hello";
char JsonStr[77];
char JsonStr2[94];

char warning1[40] = "Flammable gas detected.";  //23
char warning2[40] = "The temperature is too high."; //28
char warning3[40] = "High humidity detected.";  //23
char warning4[40] = "Abnormal air pressure detected.";  //31
char str[20] = "Current Data";  //12
char mode1[30] = "0 Transmission mode"; //19
char mode2[30] = "1 WOR Sending mode";  //18
char mode3[30] = "2 WOR Receiving mode";//20
char mode4[30] = "3 Sleep mode";    //12

//-------------------------------------------------------------------------------------------------
// 关于线程的一些宏定义
//-------------------------------------------------------------------------------------------------

#define THREAD_PRIORITY    10     //线程的优先级
#define THREAD_STACK_SIZE  1024   //线程栈的大小
#define THREAD_TIMESLICE   10      //线程时间片大小

//-------------------------------------------------------------------------------------------------
// 定时器和线程的函数声明
//-------------------------------------------------------------------------------------------------
static rt_timer_t timer1;
static void timerout1(void* parameter);

static char thread1_stack[1024];
static char thread2_stack[1024];
static struct rt_thread thread1;
static struct rt_thread thread2;
static void thread1_entry(void *parameter);
static void thread2_entry(void *parameter);

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

static rt_thread_t p_data_show_ui_thread = NULL;

static void data_show_ui_thread(void *arg)
{
    //ui初始化
    lv_ex_gauge_1();

    for ( ; ; )
    {
        lv_tick_inc(10);
        rt_thread_delay(10);   // task sleep 10 ms
    }
}

int data_show_ui_create()
{
    /* create the thread */
    p_data_show_ui_thread = rt_thread_create("data_show_ui_thread",
                            data_show_ui_thread,
                            NULL,           // arg
                            1024*20,        // TODO statck size
                            4,              // TODO priority
                            10);            // slice ticks

    if (p_data_show_ui_thread == NULL)
    {
        rt_kprintf("create data_show_ui thread fail!\r\n");
        return -1;
    }
    rt_thread_startup(p_data_show_ui_thread);
    return 0;
}

//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------


rt_thread_t sensor_data_get_thread = NULL;

void sensor_data_get(unsigned char num)
{
    ls1x_watchdog_feed(1800000);   //喂狗并定义下一次的超时时间为30分钟
    switch(num)
    {
        case 0:
            light = TSL2561FN_RD_Data();
            break;
        case 1:
            HDC_Get_Temp_Hum(&temp, &hum);
            break;
        case 2:
            SPL06_Get_Prs(&press, &eleva);
            press = press/1000.0;
            break;
        case 3:
            sta = MQ_Read_Status();
            break;
    }
}

int sensor_data_get_create()
{
    sensor_data_get_thread = rt_thread_create("sensor_data_get_thread",
                             sensor_data_get,
                             NULL,         // arg
                             1024*4,       // statck size
                             11,           // priority
                             10);          // slice ticks

    if (sensor_data_get_thread == NULL)
    {
        rt_kprintf("sensor_data_get_ thread fail!\r\n");
    }
    else
    {
        rt_thread_startup(sensor_data_get_thread);
    }
    return 0;
}


//-------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

static rt_thread_t p_lvgl_core_thread = NULL;

static void lvgl_core_thread_thread(void *arg)
{
    for ( ; ; )
    {
        lv_task_handler();
        rt_thread_delay(5);
    }
}

int lvgl_core_thread_create()
{
    p_lvgl_core_thread = rt_thread_create("lvgl_core_thread",
                                          lvgl_core_thread_thread,
                                          NULL,         // arg
                                          1024*4,   // TODO statck size
                                          5,           // TODO priority
                                          10);          // slice ticks

    if (p_lvgl_core_thread == NULL)
    {
        rt_kprintf("p_lvgl_core_thread fail!\r\n");
        return -1;
    }
    rt_thread_startup(p_lvgl_core_thread);
    return 0;
}

//-------------------------------------------------------------------------------------------------
//主函数
//-------------------------------------------------------------------------------------------------

int main(int argc, char** argv)
{
    rt_kprintf("\r\nWelcome to RT-Thread.\r\n\r\n");
    ls1x_drv_init();            /* Initialize device drivers */
    rt_ls1x_drv_init();         /* Initialize device drivers for RTT */
    install_3th_libraries(); 	/* Install 3th libraies */

    ls1x_watchdog_start(1800000);  //开启看门狗，时间设为30分钟

    //控制屏幕背光引脚
    gpio_enable(54,DIR_OUT);
    gpio_write(54,1);

    //-------------------
    //LORA_Init();
    //-------------------

    //RTC初始化
    PWM2_Time_Init();
    //I2C初始化
    I2C1_init();
    //气压传感器初始化
    SPL06_init();
    //烟雾传感器初始化
    Other_init();
    //温湿度传感器初始化
    Get_HDC_ID();
    //光照传感器初始化
    TSL_init();
    //蜂鸣器初始化
    PCA9557_Init();

    //-------------------
    KEY_IO_Config();
    UART3_Config_Init();
    UART4_Config_Init();
    UART5_Config_Init();
    //gpio_Config_Init();
    //-------------------


    lvgl_core_thread_create();//LVGL时钟线程初始化
    sensor_data_get_create();//传感器数据获取
    data_show_ui_create();//UI显示

    //------------------------------------------------------------------

    rt_thread_init(&thread1,
                   "thread1",
                   thread1_entry,
                   RT_NULL,
                   &thread1_stack[0],
                   sizeof(thread1_stack),
                   THREAD_PRIORITY-1,
                   THREAD_TIMESLICE);
    rt_thread_startup(&thread1);

    rt_thread_init(&thread2,
                   "thread2",
                   thread2_entry,
                   RT_NULL,
                   &thread2_stack[0],
                   sizeof(thread2_stack),
                   THREAD_PRIORITY,
                   THREAD_TIMESLICE);
    rt_thread_startup(&thread2);

    timer1 = rt_timer_create("timer1",
                             timerout1,
                             RT_NULL,
                             5000,   //每5s向串口发送一次各传感器数据
                             RT_TIMER_FLAG_PERIODIC);    //周期定时器
    if(timer1!=RT_NULL)
    {
        rt_timer_start(timer1);
    }

    return 0;
}

/*----------------------------------------------------------------
 * timerout1
 * 功    能：每20s通过串口向终端发送一次各传感器数据
 * 入口参数：按照线程入口函数格式
 * 返 回 值：无
 * 说明描述:
 *-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
static void timerout1(void* parameter)
{
    sprintf(JsonStr,"{{\"code\":%u,\"temp\":%.1f,\"hum\":%.1f,\"press\":%.2f,\"light\":%.2f,\"sta\":%u}\n", code, temp, hum, press, light, sta);
    printf("{{\"code\":%u,\"temp\":%.1f,\"hum\":%.1f,\"press\":%.2f,\"light\":%.2f,\"sta\":%u}\n", code, temp, hum, press, light, sta);
    ls1x_uart_write(devUART4,JsonStr,sizeof(JsonStr),NULL);

    if(sta != 0)
    {
        char buf2[47];
        sprintf(buf2, "{\"code\":%u,\"warning\":%s}", code, warning1);
        ls1x_uart_write(devUART4,buf2,sizeof(buf2),NULL);
    }
    if(temp >= 60)
    {
        char buf3[52];
        sprintf(buf3,"{\"code\":%u,\"warning\":%s}", code, warning2);
        ls1x_uart_write(devUART4,buf3,sizeof(buf3),NULL);
    }
    if(hum >= 80)
    {
        char buf4[47];
        sprintf(buf4,"{\"code\":%u,\"warning\":%s}", code, warning3);
        ls1x_uart_write(devUART4,buf4,sizeof(buf4),NULL);
    }
    if((press<70)&&(press>170))
    {
        char buf5[55];
        sprintf(buf5,"{\"code\":%u,\"warning\":%s}", code, warning4);
        ls1x_uart_write(devUART4,buf5,sizeof(buf5),NULL);
    }
}

/*----------------------------------------------------------------
 * thread1_entry
 * 功    能：LORA模块模式选择线程入口，优先级要高于传感器数据发送线程
 * 入口参数：按照线程入口函数格式
 * 返 回 值：无
 * 说明描述:模式（0-3）默认为01发送模式
 *            M0:0;M1:0时，为0 传输模式;
 *            M0:1;M1:0时，为1 WOR 发送模式;
 *            M0:0;M1:1时，为2 WOR 接收模式;
 *            M0:1;M1:1时，为3 深度休眠;
 *---------------------------------------------------------------*/

static void thread1_entry(void *parameter)
{
    while(1)
    {
        //接收数据
        ls1x_uart_read(devUART4,buff,256,NULL);//从串口读数据(接收)
        if(ls1x_uart_read(devUART4,buff,10,NULL))
        {
            if(strncmp(buff,"mode0",5) == 0)
            {
                ls1x_uart_write(devUART4,buf,5,NULL);
            }
            else
            {
                ls1x_uart_write(devUART4,"abc",3,NULL);
            }
            memset(buff,0x00,strlen(buff));
            delay_ms(1000);
        }

        if(strncmp(buff,"mode0",5) == 0) //串口打开，无线打开，透明传输
        {
            char buf1[42];//19+23
            gpio_write(M0,0);
            gpio_write(M1,0);
            sprintf(buf1,"{\"code\":%u,\"mode\":%s}\n",code,mode1);
            printf("Mode0");
            ls1x_uart_write(devUART4,buf1,sizeof(buf1),NULL);
        }
        else if(strncmp(buff,"mode1",5) == 0) //WOR 发送方
        {
            char buf1[41];//18
            gpio_write(M0,1);
            gpio_write(M1,0);
            sprintf(buf1,"{\"code\":%u,\"mode\":%s}\n",code,mode2);
            printf("Mode1");
            ls1x_uart_write(devUART4,buf1,sizeof(buf1),NULL);
        }
        else if(strncmp(buff,"mode2",5) == 0) //WOR 接收方
        {
            char buf1[43];//20
            gpio_write(M0,0);
            gpio_write(M1,1);
            sprintf(buf1,"{\"code\":%u,\"mode\":%s}\n",code,mode3);
            printf("Mode2");
            ls1x_uart_write(devUART4,buf1,sizeof(buf1),NULL);
        }
        else if(strncmp(buff,"mode3",5) == 0) //模块进入休眠（配置参数时自动唤醒）
        {
            char buf1[35];//12
            gpio_write(M0,1);
            gpio_write(M1,1);
            sprintf(buf1,"{\"code\":%u,\"mode\":%s}\n",code,mode4);
            printf("Mode3");
            ls1x_uart_write(devUART4,buf1,sizeof(buf1),NULL);
        }
        rt_thread_mdelay(1);
    }

}
/*----------------------------------------------------------------
 * thread2_entry
 * 功    能：主动向节点获取数据，各传感器在收到信息后做出的反应
 * 入口参数：按照线程入口函数格式
 * 返 回 值：无
 * 说明描述:
 *---------------------------------------------------------------*/
static void thread2_entry(void *parameter)
{
    ls1x_uart_read(devUART4,buff,256,NULL);//从串口读数据(接收)
    if(ls1x_uart_read(devUART4,buff,256,NULL))
    {
        ls1x_uart_write(devUART4,buf,sizeof(buf),NULL);
    }

    if(strncmp(buff,"current",7)==0)
    {
        sprintf(JsonStr2, "{\"code\":%u,\"str\":%s,\"temp\":%.1f,\"hum\":%.1f,\"press\":%.2f,\"light\":%.2f,\"sta\":%u}\n", code, str, temp, hum, press, light, sta);
        printf("God Damn Current");
        ls1x_uart_write(devUART4,JsonStr2,sizeof(JsonStr2),NULL);

        //SendWarn();
    }
    while(1)
    {
        rt_thread_mdelay(10);
    }

}

//-------------------------------------------------------------------
//-------------------------------------------------------------------








