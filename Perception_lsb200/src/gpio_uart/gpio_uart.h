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

// 定义LoRa模块的引脚
#define RX 58
#define TX 59
#define M0 40 //复用GPIO口为控制LORA工作模式
#define M1 41

void UART5_Config_Init(void);
void UART4_Config_Init(void);
void gpio_Config_Init(void);



#ifdef __cplusplus
}
#endif

#endif // _GPIO_UART_H
