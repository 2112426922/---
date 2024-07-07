/*
 * key.c
 *
 * created: 2024/3/5
 *  author: 
 */

 #include "key.h"

 #define KEY_UP 0

// �����жϵı�־
volatile int gpio_key_irq_flag = 0;
//״̬��ת��־
//volatile int flag = 0;


/*********************************************************************
 **��������test_gpio_key_irqhandler
 **�������ܣ������жϵĴ�����
 **�βΣ�
    @IRQn  �жϺ�
    @param ���ݸ��жϴ������Ĳ���
 **����ֵ����
 **˵����
 ********************************************************************/
void test_gpio_key_irqhandler(int IRQn, void *param)
{
    gpio_key_irq_flag = 1;
}

 /*******************************************************************
 **��������KEY_IO_Exti_Config
 **�������ܣ���ʼ��������IO��--�жϷ�
 **�βΣ���
 **����ֵ����
 **˵����
 *******************************************************************/
 void KEY_IO_Config(void)
 {
    gpio_enable(KEY_UP,DIR_IN); //����GPIOΪ����
    ls1x_install_gpio_isr(KEY_UP,INT_TRIG_LEVEL_HIGH,test_gpio_key_irqhandler,NULL);//�����ж�
    ls1x_enable_gpio_interrupt(KEY_UP);//ʹ���ж�
 }

 /*******************************************************************
**��������KEY_Exti_Test(void)
**�������ܣ������жϲ���
**�����βΣ���
**��������ֵ����
**˵����
*******************************************************************/
 unsigned char KeyScan(void)
 {
    unsigned char flag = 0;
    //������������жϵĴ������������¹�0
    if(gpio_key_irq_flag == 1)
    {
        gpio_key_irq_flag = 0;
        flag = 1;
    }
    return flag;
 }

