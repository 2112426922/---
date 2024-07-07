//---------------------------------------------------------------------------
// ��Ҫ�Ŀ�ͷ�ļ�   8600
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
// BSPͷ�ļ�
//---------------------------------------------------------------------------
#include "bsp.h"
#include "ls1x_fb.h"
#include "ls1b_gpio.h"
char LCD_display_mode[] = LCD_480x800;

//---------------------------------------------------------------------------
// lvglͷ�ļ�
//---------------------------------------------------------------------------
#include "lvgl_ui.h"
//---------------------------------------------------------------------------
// srcͷ�ļ�
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
// ����ͷ�ļ�
//---------------------------------------------------------------------------
#include "ls1x_can.h"
#include "ls1x_rtc.h"
#include "rs485_drv.h"
#include "ls1x_i2c_bus.h"

//---------------------------------------------------------------------------
// ȫ�ֱ���
//---------------------------------------------------------------------------
unsigned int sta = 0;
float temp = 0;
float hum = 0;
float press = 0;
float eleva = 0;
float light = 0;

//�豸���
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
// �����̵߳�һЩ�궨��
//-------------------------------------------------------------------------------------------------

#define THREAD_PRIORITY    10     //�̵߳����ȼ�
#define THREAD_STACK_SIZE  1024   //�߳�ջ�Ĵ�С
#define THREAD_TIMESLICE   10      //�߳�ʱ��Ƭ��С

//-------------------------------------------------------------------------------------------------
// ��ʱ�����̵߳ĺ�������
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
    //ui��ʼ��
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
    ls1x_watchdog_feed(1800000);   //ι����������һ�εĳ�ʱʱ��Ϊ30����
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
//������
//-------------------------------------------------------------------------------------------------

int main(int argc, char** argv)
{
    rt_kprintf("\r\nWelcome to RT-Thread.\r\n\r\n");
    ls1x_drv_init();            /* Initialize device drivers */
    rt_ls1x_drv_init();         /* Initialize device drivers for RTT */
    install_3th_libraries(); 	/* Install 3th libraies */

    ls1x_watchdog_start(1800000);  //�������Ź���ʱ����Ϊ30����

    //������Ļ��������
    gpio_enable(54,DIR_OUT);
    gpio_write(54,1);

    //-------------------
    //LORA_Init();
    //-------------------

    //RTC��ʼ��
    PWM2_Time_Init();
    //I2C��ʼ��
    I2C1_init();
    //��ѹ��������ʼ��
    SPL06_init();
    //����������ʼ��
    Other_init();
    //��ʪ�ȴ�������ʼ��
    Get_HDC_ID();
    //���մ�������ʼ��
    TSL_init();
    //��������ʼ��
    PCA9557_Init();

    //-------------------
    KEY_IO_Config();
    UART3_Config_Init();
    UART4_Config_Init();
    UART5_Config_Init();
    //gpio_Config_Init();
    //-------------------


    lvgl_core_thread_create();//LVGLʱ���̳߳�ʼ��
    sensor_data_get_create();//���������ݻ�ȡ
    data_show_ui_create();//UI��ʾ

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
                             5000,   //ÿ5s�򴮿ڷ���һ�θ�����������
                             RT_TIMER_FLAG_PERIODIC);    //���ڶ�ʱ��
    if(timer1!=RT_NULL)
    {
        rt_timer_start(timer1);
    }

    return 0;
}

/*----------------------------------------------------------------
 * timerout1
 * ��    �ܣ�ÿ20sͨ���������ն˷���һ�θ�����������
 * ��ڲ����������߳���ں�����ʽ
 * �� �� ֵ����
 * ˵������:
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
 * ��    �ܣ�LORAģ��ģʽѡ���߳���ڣ����ȼ�Ҫ���ڴ��������ݷ����߳�
 * ��ڲ����������߳���ں�����ʽ
 * �� �� ֵ����
 * ˵������:ģʽ��0-3��Ĭ��Ϊ01����ģʽ
 *            M0:0;M1:0ʱ��Ϊ0 ����ģʽ;
 *            M0:1;M1:0ʱ��Ϊ1 WOR ����ģʽ;
 *            M0:0;M1:1ʱ��Ϊ2 WOR ����ģʽ;
 *            M0:1;M1:1ʱ��Ϊ3 �������;
 *---------------------------------------------------------------*/

static void thread1_entry(void *parameter)
{
    while(1)
    {
        //��������
        ls1x_uart_read(devUART4,buff,256,NULL);//�Ӵ��ڶ�����(����)
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

        if(strncmp(buff,"mode0",5) == 0) //���ڴ򿪣����ߴ򿪣�͸������
        {
            char buf1[42];//19+23
            gpio_write(M0,0);
            gpio_write(M1,0);
            sprintf(buf1,"{\"code\":%u,\"mode\":%s}\n",code,mode1);
            printf("Mode0");
            ls1x_uart_write(devUART4,buf1,sizeof(buf1),NULL);
        }
        else if(strncmp(buff,"mode1",5) == 0) //WOR ���ͷ�
        {
            char buf1[41];//18
            gpio_write(M0,1);
            gpio_write(M1,0);
            sprintf(buf1,"{\"code\":%u,\"mode\":%s}\n",code,mode2);
            printf("Mode1");
            ls1x_uart_write(devUART4,buf1,sizeof(buf1),NULL);
        }
        else if(strncmp(buff,"mode2",5) == 0) //WOR ���շ�
        {
            char buf1[43];//20
            gpio_write(M0,0);
            gpio_write(M1,1);
            sprintf(buf1,"{\"code\":%u,\"mode\":%s}\n",code,mode3);
            printf("Mode2");
            ls1x_uart_write(devUART4,buf1,sizeof(buf1),NULL);
        }
        else if(strncmp(buff,"mode3",5) == 0) //ģ��������ߣ����ò���ʱ�Զ����ѣ�
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
 * ��    �ܣ�������ڵ��ȡ���ݣ������������յ���Ϣ�������ķ�Ӧ
 * ��ڲ����������߳���ں�����ʽ
 * �� �� ֵ����
 * ˵������:
 *---------------------------------------------------------------*/
static void thread2_entry(void *parameter)
{
    ls1x_uart_read(devUART4,buff,256,NULL);//�Ӵ��ڶ�����(����)
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








