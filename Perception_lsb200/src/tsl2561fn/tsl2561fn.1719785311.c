/*
 * tsl2561fn.c
 *
 * created: 2024/1/20
 *  author: 
 */

 //���մ�����

 #include "tsl2561fn.h"
 #include "ls1x_i2c_bus.h"
 #include "ls1b_gpio.h"
 #include <stdint.h>
 #include <math.h>


 #define TSL2561FN_ADDRESS 0x29        //������ַ       //41
 #define TSL2561FN_Write   0           //д����
 #define TSL2561FN_Read    1           //������

 #define ID               0x8a         //�������ID�Ĵ�����ַ
 #define CONTROL          0x80         //���ƼĴ�����ַ
 #define TIMING           0x81         //��ʱ�Ĵ�����ַ
 #define DATA0LOW         0x8c         //ͨ��0���ֽڵ�ַ
 #define DATA0HIGH        0x8d         //ͨ��0���ֽڵ�ַ
 #define DATA1LOW         0x8e         //ͨ��1���ֽڵ�ַ
 #define DATA1HIGH        0x8f         //ͨ��1���ֽڵ�ַ
 //#define LED9_IO 51

/************************************************************************
** ���ܣ�  ��TSL2561FNд������
** ������
           @reg_buf:�Ĵ�����ַ
           @buf:���ݻ�������
           @len:д���ݳ���
** ����ֵ��0,�ɹ�;-1,ʧ��.
*************************************************************************/
static char TSL_WR_Data(unsigned char reg_addr, unsigned char *buf, int len)
{
	int ret=0;

	//��ʼ�ź�
	ret = ls1x_i2c_send_start(busI2C1,NULL);
    if(ret < 0)
    {
        printf("send_start error!!!\r\n");
        return -1;
    }

 	//���ʹӻ���ַ��д����
 	ret = ls1x_i2c_send_addr(busI2C1, TSL2561FN_ADDRESS, TSL2561FN_Write);
 	if(ret < 0)
    {
        printf("send_addr error!!!\r\n");
        return -1;
    }

    //���ͼĴ�����ַ
	ret = ls1x_i2c_write_bytes(busI2C1, &reg_addr, 1);
	if(ret < 0)
    {
        printf("write_bytes_reg error!!!\r\n");
        return -1;
    }

    //��������
	ret = ls1x_i2c_write_bytes(busI2C1, buf, len);
	if(ret < 0)
    {
        printf("write_bytes error!!!\r\n");
        return -1;
    }

    //����ֹͣ�ź�
    ret = ls1x_i2c_send_stop(busI2C1,NULL);
    if(ret < 0)
    {
        printf("send_stop error!!!\r\n");
        return -1;
    }

    delay_ms(10);
    return ret;
}

/************************************************************************
** ���ܣ�  ��TSL2561FN��������
** ������
           @reg_buf:�Ĵ����ĵ�ַ
           @buf:���ݻ�������
           @len:д���ݳ���
** ����ֵ��0,�ɹ�;-1,ʧ��.
*************************************************************************/
static char TSL_RD_Data(unsigned char reg_addr,unsigned char *buf,int len)
{
	int ret=0;

 	//��ʼ�ź�
	ret = ls1x_i2c_send_start(busI2C1,NULL);
    if(ret < 0)
    {
        printf("send_start error!!!\r\n");
        return -1;
    }

 	//���ʹӻ���ַ��д����
 	ret = ls1x_i2c_send_addr(busI2C1, TSL2561FN_ADDRESS, TSL2561FN_Write);
 	if(ret < 0)
    {
        printf("send_addr_W error!!!\r\n");
        return -1;
    }

    //���ͼĴ�����ַ
	ret = ls1x_i2c_write_bytes(busI2C1, &reg_addr, 1);
	if(ret < 0)
    {
        printf("write_bytes_reg error!!!\r\n");
        return -1;
    }

    //����ֹͣ�ź�
    ret = ls1x_i2c_send_stop(busI2C1,NULL);
    if(ret < 0)
    {
        printf("send_stop error!!!\r\n");
        return -1;
    }

    //��ʼ�ź�
	ret = ls1x_i2c_send_start(busI2C1,NULL);
    if(ret < 0)
    {
        printf("send_start error!!!\r\n");
        return -1;
    }

    //���ʹӻ���ַ�Ͷ�����
 	ret = ls1x_i2c_send_addr(busI2C1, TSL2561FN_ADDRESS, TSL2561FN_Read);
 	if(ret < 0)
    {
        printf("send_addr_R error!!!\r\n");
        return -1;
    }

    //��ȡ����
    ls1x_i2c_read_bytes(busI2C1,buf,len);
    if(ret < 0)
    {
        printf("read_bytes_Data error!!!\r\n");
        return -1;
    }

    //����ֹͣ�ź�
    ret = ls1x_i2c_send_stop(busI2C1,NULL);
    if(ret < 0)
    {
        printf("send_stop error!!!\r\n");
        return -1;
    }

    delay_ms(10);
    return 0;
}


/************************************************************************
 ** ���ܣ���ȡTSL2561FN�豸��ID
 ** ˵����������IDʶ��:�ֶ�ֵ0000 = TSL2560���ֶ�ֵ0001 = TSL2561
*************************************************************************/
 void TSL_init(void)
 {
     unsigned char Device_ID;
     unsigned char TSL_Start = 0x03;        //����TSL2561Ϊ����״̬
     unsigned char TIM16X_402MS = 0x12;     //����ʱ��402����,����16��

     //gpio_enable(LED9_IO, DIR_OUT);
     //gpio_write(LED9_IO, 1);

     TSL_WR_Data(CONTROL, &TSL_Start, 1);     //����TSL2561����״̬
     TSL_WR_Data(TIMING, &TIM16X_402MS, 1);   //���û���ʱ������汶��
     TSL_RD_Data(ID, &Device_ID, 1);          //��ȡTSL2561��ID
     printf("TSL2561FN���豸IDΪ��%#x\r\n", Device_ID);
 }


 /************************************************************************
 ** ���ܣ���ȡ����ǿ��
 ** ˵����ǿ�ȴ���1000ʱ��LED9������֮��LED9��
 *************************************************************************/
 float TSL2561FN_RD_Data(void)
 {
     unsigned char  Data0Low, Data0High, Data1Low, Data1High;
     float Channel0, Channel1;
     float data = 0; //��ǿ
     float res = 0;
     TSL_RD_Data(DATA0LOW, &Data0Low, 1);
     delay_us(80);
     TSL_RD_Data(DATA0HIGH, &Data0High, 1);
     Channel0 = 256*Data0High + Data0Low;  //ͨ��0

     TSL_RD_Data(DATA1LOW, &Data1Low, 1);
     delay_us(80);
     TSL_RD_Data(DATA1HIGH, &Data1High, 1);
     Channel1 = 256*Data1High + Data1Low;  //ͨ��1

     res = Channel1/Channel0;
     if((res > 0) && (res <= 0.50))
         data = 0.0304*Channel0-0.062*Channel0*pow(res, 1.4);
     else if(res <= 0.61)
         data = 0.0224*Channel0-0.031*res*Channel0;
     else if(res <= 0.80)
         data = 0.0128*Channel0-0.0153*res*Channel0;
     else if(res <= 1.30)
         data = 0.00146*Channel0-0.00112*res*Channel0;
     else
         data = 0;

     return data;
 }

//void LED9_ON(void)
//{
    //gpio_write(LED9_IO, 0);
//}

//void LED9_OFF(void)
//{
    //gpio_write(LED9_IO, 1);
//}

