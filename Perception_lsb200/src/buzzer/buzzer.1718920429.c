/*
 * buzzer.c
 *
 * created: 2021/7/14
 *  author:
 */
 #include "buzzer.h"
 #include "ls1x_i2c_bus.h"
 #include "i2c/pca9557.h"
 //初始化
 void PCA9557_Init(void)
 {

     ls1x_i2c_initialize(busI2C0);
     ls1x_pca9557_init(busI2C0,NULL);//默认端口引脚的原始极性被保留

 }
 //打开蜂鸣器
 void Buzzer_ON(void)
 {
     unsigned char outData = 0x80;
     ls1x_pca9557_write(busI2C0,&outData,0,0);
 }
 //关闭蜂鸣器
 void Buzzer_OFF(void)
 {
     unsigned char outData = 0x00;
     ls1x_pca9557_write(busI2C0,&outData,0,0);
 }
 //间断响
void Buzzer_blink(void)
{
    Buzzer_ON();
    delay_ms(500);
    Buzzer_OFF();
    delay_ms(500);
    Buzzer_ON();
    delay_ms(500);
    Buzzer_OFF();
    delay_ms(500);
    Buzzer_ON();
    delay_ms(500);
    Buzzer_OFF();
    delay_ms(500);
    Buzzer_ON();
    delay_ms(500);
    Buzzer_OFF();
    delay_ms(500);
    //Buzzer_ON();
}


