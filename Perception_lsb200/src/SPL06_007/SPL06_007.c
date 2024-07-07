/*
 * SPL06_007.c
 *
 * created: 2024/1/20
 *  author: 
 */

 //��ѹ������

 #include "SPL06_007.h"
 #include "ls1x_i2c_bus.h"
 #include "ls1b_gpio.h"
 #include "math.h"

 #define LED10_IO 50

//��ѹ��������(sample/sec),Background ģʽʹ��
 #define  PM_RATE_1          (0<<4)      //1 measurements pr. sec.
 #define  PM_RATE_2          (1<<4)      //2 measurements pr. sec.
 #define  PM_RATE_4          (2<<4)      //4 measurements pr. sec.
 #define  PM_RATE_8          (3<<4)      //8 measurements pr. sec.
 #define  PM_RATE_16         (4<<4)      //16 measurements pr. sec.
 #define  PM_RATE_32         (5<<4)      //32 measurements pr. sec.
 #define  PM_RATE_64         (6<<4)      //64 measurements pr. sec.
 #define  PM_RATE_128        (7<<4)      //128 measurements pr. sec.

//��ѹ�ز�������(times),Background ģʽʹ��
 #define PM_PRC_1            0       //Sigle         kP=524288   ,3.6ms
 #define PM_PRC_2            1       //2 times       kP=1572864  ,5.2ms
 #define PM_PRC_4            2       //4 times       kP=3670016  ,8.4ms
 #define PM_PRC_8            3       //8 times       kP=7864320  ,14.8ms
 #define PM_PRC_16           4       //16 times      kP=253952   ,27.6ms
 #define PM_PRC_32           5       //32 times      kP=516096   ,53.2ms
 #define PM_PRC_64           6       //64 times      kP=1040384  ,104.4ms
 #define PM_PRC_128          7       //128 times     kP=2088960  ,206.8ms

//�¶Ȳ�������(sample/sec),Background ģʽʹ��
 #define  TMP_RATE_1         (0<<4)      //1 measurements pr. sec.
 #define  TMP_RATE_2         (1<<4)      //2 measurements pr. sec.
 #define  TMP_RATE_4         (2<<4)      //4 measurements pr. sec.
 #define  TMP_RATE_8         (3<<4)      //8 measurements pr. sec.
 #define  TMP_RATE_16        (4<<4)      //16 measurements pr. sec.
 #define  TMP_RATE_32        (5<<4)      //32 measurements pr. sec.
 #define  TMP_RATE_64        (6<<4)      //64 measurements pr. sec.
 #define  TMP_RATE_128       (7<<4)      //128 measurements pr. sec.

//�¶��ز�������(times),Background ģʽʹ��
 #define TMP_PRC_1           0       //Sigle
 #define TMP_PRC_2           1       //2 times
 #define TMP_PRC_4           2       //4 times
 #define TMP_PRC_8           3       //8 times
 #define TMP_PRC_16          4       //16 times
 #define TMP_PRC_32          5       //32 times
 #define TMP_PRC_64          6       //64 times
 #define TMP_PRC_128         7       //128 times

 //�豸��ַ�Ͷ�д����
 #define LGA_ADDRESS 0x76       //118
 #define LGA_Write 0
 #define LGA_Read  1

 //SPL06_MEAS_CFG
 #define MEAS_COEF_RDY       0x80
 #define MEAS_SENSOR_RDY     0x40        //��������ʼ�����
 #define MEAS_TMP_RDY        0x20        //���µ��¶�����
 #define MEAS_PRS_RDY        0x10        //���µ���ѹ����
 //uint8_t mode
 #define MEAS_CTRL_Standby               0x00    //����ģʽ
 #define MEAS_CTRL_PressMeasure          0x01    //������ѹ����
 #define MEAS_CTRL_TempMeasure           0x02    //�����¶Ȳ���
 #define MEAS_CTRL_ContinuousPress       0x05    //������ѹ����
 #define MEAS_CTRL_ContinuousTemp        0x06    //�����¶Ȳ���
 #define MEAS_CTRL_ContinuousPressTemp   0x07    //������ѹ�¶Ȳ���

 //FIFO_STS
 #define SPL06_FIFO_FULL     0x02
 #define SPL06_FIFO_EMPTY    0x01

 //INT_STS
 #define SPL06_INT_FIFO_FULL     0x04
 #define SPL06_INT_TMP           0x02
 #define SPL06_INT_PRS           0x01

 //CFG_REG
 #define SPL06_CFG_T_SHIFT   0x08    //oversampling times>8ʱ����ʹ��
 #define SPL06_CFG_P_SHIFT   0x04

 #define SP06_PSR_B2     0x00        //��ѹֵ
 #define SP06_PSR_B1     0x01
 #define SP06_PSR_B0     0x02
 #define SP06_TMP_B2     0x03        //�¶�ֵ
 #define SP06_TMP_B1     0x04
 #define SP06_TMP_B0     0x05

 #define SP06_PSR_CFG    0x06        //��ѹ��������
 #define SP06_TMP_CFG    0x07        //�¶Ȳ�������
 #define SP06_MEAS_CFG   0x08        //����ģʽ����

 #define SP06_CFG_REG    0x09
 #define SP06_INT_STS    0x0A
 #define SP06_FIFO_STS   0x0B

 #define SP06_RESET      0x0C
 #define SP06_ID         0x0D

 #define SP06_COEF       0x10        //-0x21
 #define SP06_COEF_SRCE  0x28

 /************************************************************************
** ���ܣ�  ��SPL06д������
** ������
           @reg_buf:�Ĵ�����ַ
           @buf:���ݻ�������
           @len:д���ݳ���
** ����ֵ��0,�ɹ�;-1,ʧ��.
*************************************************************************/
static char SPL06_WR_Data(unsigned char reg_addr, unsigned char *buf, int len)
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
 	ret = ls1x_i2c_send_addr(busI2C1, LGA_ADDRESS, LGA_Write);
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

	return ret;
}

/************************************************************************
** ���ܣ�  ��SPL06��������
** ������
           @reg_buf:�Ĵ����ĵ�ַ
           @buf:���ݻ�������
           @len:д���ݳ���
** ����ֵ��0,�ɹ�;-1,ʧ��.
*************************************************************************/
static char SPL06_RD_Data(unsigned char reg_addr,unsigned char *buf,int len)
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
 	ret = ls1x_i2c_send_addr(busI2C1, LGA_ADDRESS, LGA_Write);
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
 	ret = ls1x_i2c_send_addr(busI2C1, LGA_ADDRESS, LGA_Read);
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

    return 0;
}

float _kT,_kP;
int16_t _CX_buf[9] = {0};
int32_t _Cx_buf[9] = {0};
uint8_t reset = 0x89;

/************************************************************************
** ���ܣ�  ��ʼ����
** ������
           @uint8_t mode

*************************************************************************/
static void SPL06_start(uint8_t mode)
{
    SPL06_WR_Data(SP06_MEAS_CFG, &mode, 1);
}

/************************************************************************
** ���ܣ�  �����¶ȵĲ����ٶȺ͹�������
** ������
           @uint8_t rate  -- �����ٶ�ֵ
           @uint8_t oversampling -- ��������ֵ

*************************************************************************/
static void SPL06_Config_Temp(uint8_t rate, uint8_t oversampling)
{
    uint8_t Config_Temp_Data = 0;
    uint8_t temp = 0;

    switch(oversampling)
    {
        case TMP_PRC_1:
            _kT = 524288;
            break;
        case TMP_PRC_2:
            _kT = 1572864;
            break;
        case TMP_PRC_4:
            _kT = 3670016;
            break;
        case TMP_PRC_8:
            _kT = 7864320;
            break;
        case TMP_PRC_16:
            _kT = 253952;
            break;
        case TMP_PRC_32:
            _kT = 516096;
            break;
        case TMP_PRC_64:
            _kT = 1040384;
            break;
        case TMP_PRC_128:
            _kT = 2088960;
            break;
    }

    Config_Temp_Data = rate | oversampling | 0x80;

    SPL06_WR_Data(SP06_TMP_CFG, &Config_Temp_Data, 1);
    if(oversampling > TMP_PRC_8)
    {
        SPL06_RD_Data(SP06_CFG_REG, &temp, 1);
        Config_Temp_Data = temp | SPL06_CFG_T_SHIFT;
        SPL06_WR_Data(SP06_CFG_REG, &Config_Temp_Data, 1);
    }
}

/************************************************************************
** ���ܣ�  ������ѹ�Ĳ����ٶȺ͹�������
** ������
           @uint8_t rate  -- �����ٶ�ֵ
           @uint8_t oversampling -- ��������ֵ

*************************************************************************/
static void SPL06_Config_Prs(uint8_t rate,uint8_t oversampling)
{
    uint8_t Config_Prs_Data = 0;
    uint8_t temp = 0;
    switch(oversampling)
    {
        case PM_PRC_1:
            _kP = 524288;
            break;
        case PM_PRC_2:
            _kP = 1572864;
            break;
        case PM_PRC_4:
            _kP = 3670016;
            break;
        case PM_PRC_8:
            _kP = 7864320;
            break;
        case PM_PRC_16:
            _kP = 253952;
            break;
        case PM_PRC_32:
            _kP = 516096;
            break;
        case PM_PRC_64:
            _kP = 1040384;
            break;
        case PM_PRC_128:
            _kP = 2088960;
            break;
    }
    Config_Prs_Data = rate | oversampling;
    SPL06_WR_Data(SP06_PSR_CFG, &Config_Prs_Data, 1);
    if(oversampling > PM_PRC_8)
    {
        SPL06_RD_Data(SP06_CFG_REG, &temp, 1);
        Config_Prs_Data = temp | SPL06_CFG_P_SHIFT;
        SPL06_WR_Data(SP06_CFG_REG, &Config_Prs_Data, 1);
    }
}

/************************************************************************
** ���ܣ�  ��ȡУ׼����
** ������

*************************************************************************/
static void SPL06_Get_COFE(void)
{
    uint8_t coef[18] = {0};
    //��ȡУ׼����
    SPL06_RD_Data(SP06_COEF, coef, 18);
    _CX_buf[0] = ((int16_t)coef[0] << 4 ) | (coef[1] >> 4);
    _CX_buf[0] = (_CX_buf[0] & 0x0800) ? (0xF000 | _CX_buf[0]) : _CX_buf[0];
    _CX_buf[1] = ((int16_t)(coef[1] & 0x0F) << 8 ) | coef[2];
    _CX_buf[1] = (_CX_buf[1] & 0x0800) ? (0xF000 | _CX_buf[1]) : _CX_buf[1];
    _Cx_buf[2] = ((int32_t)coef[3] << 12 ) | ((int32_t)coef[4] << 4 ) | ((int32_t)coef[5] >> 4);
    _Cx_buf[2] = (_Cx_buf[2] & 0x080000) ? (0xFFF00000 | _Cx_buf[2]) : _Cx_buf[2];
    _Cx_buf[3] = ((int32_t)(coef[5] & 0x0F) << 16 ) + ((int32_t)coef[6] << 8 ) + (int16_t)coef[7];
    _Cx_buf[3] = (_Cx_buf[3] & 0x080000) ? (0xFFF00000 | _Cx_buf[3]) : _Cx_buf[3];
    _CX_buf[4] = ((int16_t)coef[8] << 8 ) | coef[9];
    //_CX_buf[4] = (_CX_buf[4] & 0x0800)?(0xF000 | _CX_buf[4]) : _CX_buf[4];
    _CX_buf[5] = ((int16_t)coef[10] << 8 ) | coef[11];
    //_CX_buf[5] = (_CX_buf[5] & 0x0800)?(0xF000 | _CX_buf[5]) : _CX_buf[5];
    _CX_buf[6] = ((int16_t)coef[12] << 8 ) | coef[13];
    //_CX_buf[6] = (_CX_buf[6] & 0x0800)?(0xF000 | _CX_buf[6]) : _CX_buf[6];
    _CX_buf[7] = ((int16_t)coef[14] << 8 ) | coef[15];
    //_CX_buf[7] = (_CX_buf[7] & 0x0800)?(0xF000 | _CX_buf[7]) : _CX_buf[7];
    _CX_buf[8] = ((int16_t)coef[16] << 8 ) | coef[17];
    //_CX_buf[8] = (_CX_buf[8] & 0x0800)?(0xF000 | _CX_buf[8]) : _CX_buf[8];
}

/************************************************************************
** ���ܣ� ��ʼ��SPL06
** ˵����

*************************************************************************/
uint8_t SPL06_init(void)
{
    uint8_t id;

    gpio_enable(LED10_IO,DIR_OUT);
    gpio_write(LED10_IO, 1);

    SPL06_RD_Data(SP06_ID, &id, 1);
    if(id != 0x10)
    {
        printf("SPL06 id error !!!\r\n");
        return -1;
    }
#if 0
    if(SPL06_WR_Data(SP06_RESET, &reset, 1) != 0)
    {
        printf("SPL06 reset  fail\r\n");
        return -1;
    }

    delay_ms(200);
#endif

    //��ȡУ׼����
    SPL06_Get_COFE();

    SPL06_Config_Prs(PM_RATE_32, PM_PRC_8);
    SPL06_Config_Temp(TMP_RATE_32, TMP_PRC_8);

    SPL06_start(MEAS_CTRL_ContinuousPressTemp); //������������ѹ�¶Ȳ���
    delay_ms(20);

    return 0;
}

/************************************************************************
** ���ܣ���ȡSPL06����ѹֵ
** ��������
** ����ֵ��int32_t adc ��ѹԭʼֵ
*************************************************************************/
static int32_t SPL06_Get_Prs_Adc(void)
{
    uint8_t buf[3] = {0};
    int32_t PressureAdc;

    SPL06_RD_Data(SP06_PSR_B2, buf, 3);
    PressureAdc = (int32_t)(buf[0]<<16) | (int32_t)(buf[1]<<8) | (int32_t)buf[2];
    PressureAdc = (PressureAdc & 0x800000) ? (0xFF000000 | PressureAdc) : PressureAdc;

    return PressureAdc;
}

/************************************************************************
** ���ܣ���ȡSPL06������ֵ
** ��������
** ����ֵ��int32_t adc ����ԭʼֵ
*************************************************************************/
static int32_t SPL06_Get_Temp_Adc(void)
{
    uint8_t buf[3] = {0};
    int32_t TemperatureAdc;

    SPL06_RD_Data(SP06_TMP_B2, buf, 3);
    TemperatureAdc = (int32_t)(buf[0]<<16) | (int32_t)(buf[1]<<8) | (int32_t)buf[2];
    TemperatureAdc = (TemperatureAdc & 0x800000) ? (0xFF000000 | TemperatureAdc) : TemperatureAdc;

    return TemperatureAdc;
}

/************************************************************************
** ���ܣ��ڻ�ȡԭʼֵ�Ļ����ϣ����ظ���У׼���ѹ��ֵ
** ������   @float *_Press
            @float *_Eleva
** ����ֵ����
*************************************************************************/
void SPL06_Get_Prs(float *_Press, float *_Eleva)
{
    float Traw_sc, Praw_sc;
    float temp1, temp2;

    Praw_sc = SPL06_Get_Prs_Adc() / (float)_kP;
    Traw_sc = SPL06_Get_Temp_Adc() / (float)_kT;

    //������ѹ
    temp1 = _Cx_buf[3] + Praw_sc * (_CX_buf[6] + Praw_sc * _CX_buf[8]);
    temp2 = Traw_sc * Praw_sc * (_CX_buf[5] + Praw_sc * _CX_buf[7]);
    *_Press = _Cx_buf[2] + Praw_sc * temp1 + Traw_sc * _CX_buf[4] + temp2;

    //���㺣��
    *_Eleva = 44330 * (1 - pow(*_Press / 101325, 1 / 5.255));
}

/************************************************************************
** ���ܣ��ڻ�ȡԭʼֵ�Ļ����ϣ����ظ���У׼����¶�ֵ
** ������   @float *_Temp
** ����ֵ����
*************************************************************************/
void SPL06_Get_Temp(float *_Temp)
{
    float Traw_sc;

    Traw_sc = SPL06_Get_Temp_Adc() / (float)_kT;

    //�����¶�
    *_Temp = 0.5 * _CX_buf[0] + Traw_sc * _CX_buf[1];
}

#if 0
/************************************************************************
** ���ܣ����㲢��ȡSPL06����ѹֵ������ֵ
** ������
    @float *_Temp
    @float *_Press
** ����ֵ����
*************************************************************************/
void SPL06_Meas_Read_results(float *_Temp, float *_Press, float *_Eleva)
{
    float Traw_sc, Praw_sc;
    float temp1, temp2;

    Praw_sc = SPL06_Get_Prs_Adc() / (float)_kP;
    Traw_sc = SPL06_Get_Temp_Adc() / (float)_kT;

    //�����¶�
    *_Temp = 0.5 * _CX_buf[0] + Traw_sc * _CX_buf[1];

    //������ѹ
    temp1 = _CX_buf[3] + Praw_sc * (_CX_buf[6] + Praw_sc * _CX_buf[8]);
    temp2 = Traw_sc * Praw_sc * (_CX_buf[5] + Praw_sc * _CX_buf[7]);
    *_Press = _CX_buf[2] + Praw_sc * temp1 + Traw_sc * _CX_buf[4] + temp2;

    //���㺣��
    *_Eleva = 44330 * (1 - pow(*_Press / 100 / 1013.25, 1 / 5.255));
}
#endif

void LED10_ON(void)
{
    gpio_write(LED10_IO, 0);
}

void LED10_OFF(void)
{
    gpio_write(LED10_IO, 1);
}


/************************************************************************
 ** ���ܣ���ȡLGA8�豸��ID
 ** ˵��������豸ID��0x10,������
*************************************************************************/
void Get_SPL06_ID(void)
{
     unsigned char Device_ID;

     SPL06_RD_Data(SP06_ID, &Device_ID, 1);

     printf("LGA8���豸IDΪ��%#x\r\n",Device_ID);
}


