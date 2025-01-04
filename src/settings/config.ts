interface PlayerPreferenceOption {
  label: string;
  value: string;
  backgroundColor?: string;
  textColor?: string;
}

interface PlayerPreferenceConfigBase {
  id: string;
  onChangeInSetup: boolean;
  label: string;
  visibleCondition?: {
    id: string;
    values: (string | number)[];
  };
}

interface PlayerPreferenceSelectConfig extends PlayerPreferenceConfigBase {
  defaultValue: string;
  options: PlayerPreferenceOption[];
  type: 'select';
}

interface PlayerPreferenceSliderConfig extends PlayerPreferenceConfigBase {
  defaultValue: number;
  sliderConfig: {
    step: number;
    padding: number;
    range: {
      min: number;
      max: number;
    };
  };
  type: 'slider';
}

type PlayerPreferenceConfig =
  | PlayerPreferenceSelectConfig
  | PlayerPreferenceSliderConfig;

interface PlayerPreferenceTab {
  id: string;
  config: Record<string, PlayerPreferenceConfig>;
}

const supportedColorHexColorMap: Record<SupportedColor, string> = {
  black: '#000000',
  blue: '#0000ff',
  green: '#008000',
  orange: '#ffa500',
  purple: '#800080',
  red: '#ff0000',
  white: '#ffffff',
  yellow: '#ffff00',
};

const supportedColorBackgroundPositionMap: Record<SupportedColor, string> = {
  black: `0%`,
  blue: `${100 / 7}%`,
  green: `${200 / 7}%`,
  orange: `${300 / 7}%`,
  purple: `${400 / 7}%`,
  red: `${500 / 7}%`,
  white: `${600 / 7}%`,
  yellow: `100%`,
};

const getSettingsConfig = (): Record<string, PlayerPreferenceTab> => ({
  layout: {
    id: 'layout',
    config: {
      twoColumnsLayout: {
        id: 'twoColumnsLayout',
        onChangeInSetup: true,
        defaultValue: 'disabled',
        label: _('Two column layout'),
        type: 'select',
        options: [
          {
            label: _('Enabled'),
            value: 'enabled',
          },
          {
            label: _('Disabled (single column)'),
            value: 'disabled',
          },
        ],
      },
      columnSizes: {
        id: 'columnSizes',
        onChangeInSetup: true,
        label: _('Column sizes'),
        defaultValue: 50,
        visibleCondition: {
          id: 'twoColumnsLayout',
          values: [PREF_ENABLED],
        },
        sliderConfig: {
          step: 5,
          padding: 0,
          range: {
            min: 30,
            max: 70,
          },
        },
        type: 'slider',
      },
      // [PREF_SINGLE_COLUMN_MAP_SIZE]: {
      //   id: PREF_SINGLE_COLUMN_MAP_SIZE,
      //   onChangeInSetup: true,
      //   label: _("Map size"),
      //   defaultValue: 100,
      //   visibleCondition: {
      //     id: "twoColumnsLayout",
      //     values: [DISABLED],
      //   },
      //   sliderConfig: {
      //     step: 5,
      //     padding: 0,
      //     range: {
      //       min: 30,
      //       max: 100,
      //     },
      //   },
      //   type: "slider",
      // },
      [PREF_CARD_SIZE_IN_LOG]: {
        id: PREF_CARD_SIZE_IN_LOG,
        onChangeInSetup: true,
        label: _('Size of cards in log'),
        defaultValue: 0,
        sliderConfig: {
          step: 5,
          padding: 0,
          range: {
            min: 0,
            max: 90,
          },
        },
        type: 'slider',
      },
      [PREF_CARD_INFO_IN_TOOLTIP]: {
        id: PREF_CARD_INFO_IN_TOOLTIP,
        onChangeInSetup: false,
        defaultValue: DISABLED,
        label: _('Show card info in tooltip'),
        type: 'select',
        options: [
          {
            label: _('Enabled'),
            value: ENABLED,
          },
          {
            label: _('Disabled (card image only)'),
            value: DISABLED,
          },
        ],
      },
    },
  },
  colors: {
    id: 'colors',
    config: {
      [PREF_SELECTABLE_COLOR]: {
        id: PREF_SELECTABLE_COLOR,
        onChangeInSetup: true,
        defaultValue: 'yellow',
        label: _('Selectable color'),
        type: 'select',
        options: [
          {
            label: _('Black'),
            value: 'black',
            backgroundColor: supportedColorHexColorMap.black,
            textColor: 'white',
          },
          {
            label: _('Blue'),
            value: 'blue',
            backgroundColor: supportedColorHexColorMap.blue,
          },
          {
            label: _('Green'),
            value: 'green',
            backgroundColor: supportedColorHexColorMap.green,
          },
          {
            label: _('Orange'),
            value: 'orange',
            backgroundColor: supportedColorHexColorMap.orange,
          },
          {
            label: _('Purple'),
            value: 'purple',
            backgroundColor: supportedColorHexColorMap.purple,
          },
          {
            label: _('Red'),
            value: 'red',
            backgroundColor: supportedColorHexColorMap.red,
          },
          {
            label: _('White'),
            value: 'white',
          },
          {
            label: _('Yellow'),
            value: 'yellow',
            backgroundColor: supportedColorHexColorMap.yellow,
          },
        ],
      },
      [PREF_SELECTED_COLOR]: {
        id: PREF_SELECTED_COLOR,
        onChangeInSetup: true,
        defaultValue: 'blue',
        label: _('Selected color'),
        type: 'select',
        options: [
          {
            label: _('Black'),
            value: 'black',
          },
          {
            label: _('Blue'),
            value: 'blue',
          },
          {
            label: _('Green'),
            value: 'green',
          },
          {
            label: _('Orange'),
            value: 'orange',
          },
          {
            label: _('Purple'),
            value: 'purple',
          },
          {
            label: _('Red'),
            value: 'red',
          },
          {
            label: _('White'),
            value: 'white',
          },
          {
            label: _('Yellow'),
            value: 'yellow',
          },
        ],
      },
      [PREF_SPENT_COLOR]: {
        id: PREF_SPENT_COLOR,
        onChangeInSetup: true,
        defaultValue: 'none',
        label: _('Spent color'),
        type: 'select',
        options: [
          {
            label: _('Black'),
            value: 'black',
          },
          {
            label: _('Blue'),
            value: 'blue',
          },
          {
            label: _('Green'),
            value: 'green',
          },
          {
            label: _('Orange'),
            value: 'orange',
          },
          {
            label: _('Purple'),
            value: 'purple',
          },
          {
            label: _('Red'),
            value: 'red',
          },
          {
            label: _('White'),
            value: 'white',
          },
          {
            label: _('Yellow'),
            value: 'yellow',
          },
          {
            label: _('None (show marker)'),
            value: 'none',
          },
        ],
      },
    },
  },
  gameplay: {
    id: 'gameplay',
    config: {
      [PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY]: {
        id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
        onChangeInSetup: false,
        defaultValue: DISABLED,
        label: _('Confirm end of turn and player switch only'),
        type: 'select',
        options: [
          {
            label: _('Enabled'),
            value: PREF_ENABLED,
          },
          {
            label: _('Disabled (confirm every move)'),
            value: PREF_DISABLED,
          },
        ],
      },
      [PREF_SHOW_ANIMATIONS]: {
        id: PREF_SHOW_ANIMATIONS,
        onChangeInSetup: false,
        defaultValue: PREF_ENABLED,
        label: _('Show animations'),
        type: 'select',
        options: [
          {
            label: _('Enabled'),
            value: PREF_ENABLED,
          },
          {
            label: _('Disabled'),
            value: PREF_DISABLED,
          },
        ],
      },
      [PREF_ANIMATION_SPEED]: {
        id: PREF_ANIMATION_SPEED,
        onChangeInSetup: false,
        label: _('Animation speed'),
        defaultValue: 1600,
        visibleCondition: {
          id: PREF_SHOW_ANIMATIONS,
          values: [PREF_ENABLED],
        },
        sliderConfig: {
          step: 100,
          padding: 0,
          range: {
            min: 100,
            max: 2000,
          },
        },
        type: 'slider',
      },
    },
  },
});
