/*
 * key.c
 *
 * created: 2024/3/5
 *  author: 
 */

 #include "key.h"

 #define KEY_UP 0

// 按键中断的标志
volatile int gpio_key_irq_flag = 0;
//状态反转标志
//volatile int flag = 0;


/*********************************************************************
 **函数名：test_gpio_key_irqhandler
 **函数功能：按键中断的处理函数
 **形参：
    @IRQn  中断号
    @param 传递给中断处理函数的参数
 **返回值：无
 **说明：
 ********************************************************************/
void test_gpio_key_irqhandler(int IRQn, void *param)
{
    gpio_key_irq_flag = 1;
}

 /*******************************************************************
 **函数名：KEY_IO_Exti_Config
 **函数功能：初始化按键的IO口--中断法
 **形参：无
 **返回值：无
 **说明：
 *******************************************************************/
 void KEY_IO_Config(void)
 {
    gpio_enable(KEY_UP,DIR_IN); //配置GPIO为输入
    ls1x_install_gpio_isr(KEY_UP,INT_TRIG_LEVEL_HIGH,test_gpio_key_irqhandler,NULL);//配置中断
    ls1x_enable_gpio_interrupt(KEY_UP);//使能中断
 }

 /*******************************************************************
**函数名：KEY_Exti_Test(void)
**函数功能：按键中断测试
**函数形参：无
**函数返回值：无
**说明：
*******************************************************************/
 unsigned char KeyScan(void)
 {
    unsigned char flag = 0;
    //如果触发按键中断的处理函数，则重新归0
    if(gpio_key_irq_flag == 1)
    {
        gpio_key_irq_flag = 0;
        flag = 1;
    }
    return flag;
 }

