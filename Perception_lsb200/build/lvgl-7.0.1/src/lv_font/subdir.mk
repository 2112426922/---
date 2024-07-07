#
# Auto-Generated file. Do not edit!
#

# Add inputs and outputs from these tool invocations to the build variables
C_SRCS += \
../lvgl-7.0.1/src/lv_font/hz_msyh_3500_16.c \
../lvgl-7.0.1/src/lv_font/hz_msyh_3500_24.c \
../lvgl-7.0.1/src/lv_font/hz_simsun_3500_16.c \
../lvgl-7.0.1/src/lv_font/hz_simsun_3500_24.c \
../lvgl-7.0.1/src/lv_font/lv_font.c \
../lvgl-7.0.1/src/lv_font/lv_font_dejavu_16_persian_hebrew.c \
../lvgl-7.0.1/src/lv_font/lv_font_fmt_txt.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_12.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_12_subpx.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_14.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_16.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_18.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_20.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_22.c \
../lvgl-7.0.1/src/lv_font/lv_font_montserrat_24.c \
../lvgl-7.0.1/src/lv_font/lv_font_unscii_8.c

OBJS += \
./lvgl-7.0.1/src/lv_font/hz_msyh_3500_16.o \
./lvgl-7.0.1/src/lv_font/hz_msyh_3500_24.o \
./lvgl-7.0.1/src/lv_font/hz_simsun_3500_16.o \
./lvgl-7.0.1/src/lv_font/hz_simsun_3500_24.o \
./lvgl-7.0.1/src/lv_font/lv_font.o \
./lvgl-7.0.1/src/lv_font/lv_font_dejavu_16_persian_hebrew.o \
./lvgl-7.0.1/src/lv_font/lv_font_fmt_txt.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_12.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_12_subpx.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_14.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_16.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_18.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_20.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_22.o \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_24.o \
./lvgl-7.0.1/src/lv_font/lv_font_unscii_8.o

C_DEPS += \
./lvgl-7.0.1/src/lv_font/hz_msyh_3500_16.d \
./lvgl-7.0.1/src/lv_font/hz_msyh_3500_24.d \
./lvgl-7.0.1/src/lv_font/hz_simsun_3500_16.d \
./lvgl-7.0.1/src/lv_font/hz_simsun_3500_24.d \
./lvgl-7.0.1/src/lv_font/lv_font.d \
./lvgl-7.0.1/src/lv_font/lv_font_dejavu_16_persian_hebrew.d \
./lvgl-7.0.1/src/lv_font/lv_font_fmt_txt.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_12.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_12_subpx.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_14.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_16.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_18.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_20.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_22.d \
./lvgl-7.0.1/src/lv_font/lv_font_montserrat_24.d \
./lvgl-7.0.1/src/lv_font/lv_font_unscii_8.d

# Each subdirectory must supply rules for building sources it contributes
lvgl-7.0.1/src/lv_font/%.o: ../lvgl-7.0.1/src/lv_font/%.c
	@echo 'Building file: $<'
	@echo 'Invoking: SDE Lite C Compiler'
	D:/LoongIDE/mips-2011.03/bin/mips-sde-elf-gcc.exe -mips32 -G0 -EL -msoft-float -DLIB_LVGL -DLS1B -DOS_RTTHREAD  -O0 -g -Wall -c -fmessage-length=0 -pipe -I"../" -I"../include" -I"$(GCC_SPECS)/$(OS)/lvgl-7.0.1" -I"../ls1x-drv/include" -I"../RTThread/bsp-ls1x" -I"../RTThread/components/dfs/include" -I"../RTThread/components/drivers/include" -I"../RTThread/components/finsh" -I"../RTThread/components/libc/time" -I"../RTThread/include" -I"../RTThread/port/include" -I"../RTThread/port/mips" -I"../src/buzzer" -I"../src/gpio_uart" -I"../src/hdc2080" -I"../src/key" -I"../src/lvgl_ui" -I"../src/other" -I"../src/relay" -I"../src/SPL06_007" -I"../src/tsl2561fn" -I"../src/user_drv" -MMD -MP -MF"$(@:%.o=%.d)" -MT"$(@)" -o "$@" "$<"
	@echo 'Finished building: $<'
	@echo ' '

