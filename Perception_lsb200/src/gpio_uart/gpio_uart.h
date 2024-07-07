/*
 * gpio_uart.h
 *
 * created: 2024/1/8
 *  author:
 */

#ifndef _GPIO_UART_H
#define _GPIO_UART_H

#ifdef __cplusplus
extern "C" {
#endif

// ����LoRaģ�������
#define RX 58
#define TX 59
#define M0 40 //����GPIO��Ϊ����LORA����ģʽ
#define M1 41

void UART5_Config_Init(void);
void UART4_Config_Init(void);
void gpio_Config_Init(void);



#ifdef __cplusplus
}
#endif

#endif // _GPIO_UART_H
