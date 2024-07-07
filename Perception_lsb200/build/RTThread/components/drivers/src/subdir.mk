#
# Auto-Generated file. Do not edit!
#

# Add inputs and outputs from these tool invocations to the build variables
C_SRCS += \
../RTThread/components/drivers/src/completion.c \
../RTThread/components/drivers/src/dataqueue.c \
../RTThread/components/drivers/src/pipe.c \
../RTThread/components/drivers/src/ringblk_buf.c \
../RTThread/components/drivers/src/ringbuffer.c \
../RTThread/components/drivers/src/waitqueue.c \
../RTThread/components/drivers/src/workqueue.c

OBJS += \
./RTThread/components/drivers/src/completion.o \
./RTThread/components/drivers/src/dataqueue.o \
./RTThread/components/drivers/src/pipe.o \
./RTThread/components/drivers/src/ringblk_buf.o \
./RTThread/components/drivers/src/ringbuffer.o \
./RTThread/components/drivers/src/waitqueue.o \
./RTThread/components/drivers/src/workqueue.o

C_DEPS += \
./RTThread/components/drivers/src/completion.d \
./RTThread/components/drivers/src/dataqueue.d \
./RTThread/components/drivers/src/pipe.d \
./RTThread/components/drivers/src/ringblk_buf.d \
./RTThread/components/drivers/src/ringbuffer.d \
./RTThread/components/drivers/src/waitqueue.d \
./RTThread/components/drivers/src/workqueue.d

# Each subdirectory must supply rules for building sources it contributes
RTThread/components/drivers/src/%.o: ../RTThread/components/drivers/src/%.c
	@echo 'Building file: $<'
	@echo 'Invoking: SDE Lite C Compiler'
	D:/LoongIDE/mips-2011.03/bin/mips-sde-elf-gcc.exe -mips32 -G0 -EL -msoft-float -DLIB_LVGL -DLS1B -DOS_RTTHREAD  -O0 -g -Wall -c -fmessage-length=0 -pipe -I"../" -I"../include" -I"$(GCC_SPECS)/$(OS)/lvgl-7.0.1" -I"../ls1x-drv/include" -I"../RTThread/bsp-ls1x" -I"../RTThread/components/dfs/include" -I"../RTThread/components/drivers/include" -I"../RTThread/components/finsh" -I"../RTThread/components/libc/time" -I"../RTThread/include" -I"../RTThread/port/include" -I"../RTThread/port/mips" -I"../src/buzzer" -I"../src/gpio_uart" -I"../src/hdc2080" -I"../src/key" -I"../src/lvgl_ui" -I"../src/other" -I"../src/relay" -I"../src/SPL06_007" -I"../src/tsl2561fn" -I"../src/user_drv" -MMD -MP -MF"$(@:%.o=%.d)" -MT"$(@)" -o "$@" "$<"
	@echo 'Finished building: $<'
	@echo ' '

